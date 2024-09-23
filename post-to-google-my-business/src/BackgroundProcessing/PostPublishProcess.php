<?php

namespace PGMB\BackgroundProcessing;

use PGMB\API\CachedGoogleMyBusiness;
use PGMB\Google\LocalPostEditMask;
use PGMB\ParseFormFields;
use PGMB\PostTypes\GooglePostEntity;
use PGMB\PostTypes\GooglePostEntityRepository;
use PGMB\Vendor\TypistTech\WPAdminNotices\AbstractNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\StickyNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\Store;
use PGMB_Vendor_WP_Background_Process as Background_Process;
use WP_Error;
class PostPublishProcess extends Background_Process {
    protected $action = 'mbp_background_process';

    protected $api;

    protected $repository;

    /**
     * @var Store
     */
    protected $admin_notice_store;

    public function __construct( CachedGoogleMyBusiness $api, GooglePostEntityRepository $repository, Store $admin_notice_store ) {
        parent::__construct();
        $this->api = $api;
        $this->repository = $repository;
        $this->admin_notice_store = $admin_notice_store;
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over.
     *
     * @return mixed
     */
    protected function task( $item ) {
        //		do_action_ref_array($item['action'], $item['args']);
        call_user_func_array( [$this, $item['action']], $item['args'] );
        return false;
    }

    public function delete_post( $user_key, $post_name ) {
        try {
            $this->api->set_user_id( $user_key );
            $this->api->delete_post( $post_name );
        } catch ( \Exception $e ) {
            error_log( sprintf( __( 'Failed to delete post %s from GMB: %s', 'post-to-google-my-business' ), (string) $post_name, $e->getMessage() ) );
        }
    }

    public function update_status( $entity_id ) {
        $entity = $this->repository->find_by_id( (int) $entity_id );
        if ( !$entity instanceof GooglePostEntity ) {
            return false;
        }
        try {
            $this->api->set_user_id( $entity->get_user_key() );
            $updated_post = $this->api->get_post( $entity->get_post_name() );
            $entity->set_post_success( $updated_post->name, $updated_post->state, $updated_post->searchUrl );
        } catch ( \Exception $e ) {
            $entity->set_post_state( null )->set_post_failure( sprintf( __( 'Updating status failed: %s', 'post-to-google-my-business' ), $e->getMessage() ) );
        }
        $this->repository->persist( $entity );
        return false;
    }

    public function mbp_create_google_post( $post_id, $location, $user_key = false ) {
        $this->create_google_post( $post_id, $location, $user_key );
    }

    public function mbp_delete_gmb_post( $user_key, $post_name ) {
        $this->delete_post( $user_key, $post_name );
    }

    public function create_google_post( $post_id, $location, $user_key = false ) {
        /*
         * user_key is not set pre 3.0.0, any posts that were scheduled before installing 3.0.0 will not set have user_key value,
         *
         * Can not rely on default location because if it is changed to a location on another account, the user_key will be incorrect
         */
        $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
        $parent_post_id = wp_get_post_parent_id( $post_id );
        $is_autopost = get_post_meta( $post_id, '_mbp_is_autopost', true );
        $created_post = $this->repository->find_by_parent( $post_id )->find_by_user_key( $user_key )->find_by_location( $location )->find_one();
        if ( !$created_post ) {
            $created_post = GooglePostEntity::from_api( $user_key, $location );
            $created_post->set_post_parent( $post_id );
        }
        try {
            if ( !$user_key ) {
                throw new \InvalidArgumentException(__( 'No Google account connected at time of publishing', 'post-to-google-my-business' ));
            }
            $this->api->set_user_id( $user_key );
            $post_name = $created_post->get_post_name();
            $data = new ParseFormFields($form_fields);
            //$location_data = $this->api->get_location( $location, "", true );
            //			if(!isset($location_data->locationState->isVerified) || !$location_data->locationState->isVerified || !isset($location_data->locationState->isPublished) || !$location_data->locationState->isPublished){
            //				throw new \InvalidArgumentException(__('This location is unverified, not public, or not eligible to publish posts.', 'post-to-google-my-business'));
            //			}
            $localPost = $data->getLocalPost(
                $this->api,
                $parent_post_id,
                $user_key,
                $location
            );
            if ( $post_name ) {
                $oldPost = $this->api->get_post( $post_name );
                $mask = new LocalPostEditMask($oldPost, $localPost);
                if ( !empty( $mask->getMask() ) ) {
                    //Don't dispatch if there is nothing to update
                    $localPost = apply_filters(
                        'mbp_update_post',
                        $localPost,
                        $post_id,
                        $location
                    );
                    $result = $this->api->patch_post( $post_name, $localPost, $mask );
                }
            } else {
                $localPost = apply_filters(
                    'mbp_create_post',
                    $localPost,
                    $post_id,
                    $is_autopost,
                    $location
                );
                //Backward compatibility
                $filtered_post_args = ( $is_autopost ? apply_filters( 'mbp_autopost_post_args', $localPost->getArray(), $location ) : $localPost->getArray() );
                $result = $this->api->create_post( $location, $filtered_post_args );
            }
            if ( !empty( $result ) ) {
                $created_post->set_post_success( $result->name, $result->state, $result->searchUrl );
            }
            //unset($post_errors[$location]);
        } catch ( \Throwable $e ) {
            $publishedLocalPost = new WP_Error('post_creation_error', sprintf( __( 'Failed to create/update post: %s', 'post-to-google-my-business' ), $e->getMessage() ));
            update_post_meta( $post_id, 'mbp_last_error', $publishedLocalPost->get_error_message() );
            $created_post->set_post_failure( $publishedLocalPost->get_error_message() );
            $parent_edit_link = get_edit_post_link( $parent_post_id, false );
            if ( $parent_edit_link ) {
                $anchor = esc_html__( 'Go to post', 'post-to-google-my-business' );
                $link = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( [
                    'pgmb_edit_post' => true,
                    'pgmb_post_id'   => $post_id,
                ], $parent_edit_link ) ), $anchor );
            }
            $error_message = sprintf( __( 'There recently has been an issue publishing a post to one of your GMB locations: %s.', 'post-to-google-my-business' ), $publishedLocalPost->get_error_message() );
            $this->admin_notice_store->add( new StickyNotice('post_error', '<p><strong>' . __( 'Post to Google My Business:', 'post-to-google-my-business' ) . '</strong> ' . $error_message . (( $parent_edit_link ? '<br /><br />' . $link : '' )) . '</p>', AbstractNotice::WARNING) );
        }
        $this->repository->persist( $created_post );
        sleep( 1 );
    }

    public function dispatch() {
        update_option( 'pgmb_is_busy', true );
        parent::dispatch();
    }

    protected function complete() {
        delete_option( 'pgmb_is_busy' );
        parent::complete();
    }

}
