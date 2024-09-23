<?php

namespace PGMB\Subscriber;

use PGMB\BackgroundProcessing\PostPublishProcess;
use PGMB\EventManagement\SubscriberInterface;
use PGMB\ParseFormFields;
use PGMB\PostTypes\GooglePostEntity;
use PGMB\PostTypes\GooglePostEntityRepository;
use PGMB\PostTypes\SubPost;
class PostStatusSubscriber implements SubscriberInterface {
    private $default_location;

    /**
     * @var PostPublishProcess
     */
    private $background_process;

    /**
     * @var GooglePostEntityRepository
     */
    private $repository;

    private $delete_gmb_posts;

    private $should_dispatch = false;

    private $has_dispatched = false;

    private $enabled_post_types;

    private $bypass_wp_cron;

    public function __construct(
        PostPublishProcess $process,
        GooglePostEntityRepository $repository,
        $default_location,
        $delete_gmb_posts,
        $enabled_post_types,
        $bypass_wp_cron
    ) {
        $this->background_process = $process;
        $this->default_location = $default_location;
        $this->repository = $repository;
        $this->delete_gmb_posts = $delete_gmb_posts;
        $this->enabled_post_types = $enabled_post_types;
        $this->bypass_wp_cron = $bypass_wp_cron;
    }

    public static function get_subscribed_hooks() {
        $hooks = [
            'save_post_mbp-google-subposts' => ['on_create_or_update_google_posts', 10, 3],
            'before_delete_post'            => 'before_delete_post',
            'shutdown'                      => 'should_dispatch',
        ];
        return $hooks;
    }

    public function on_create_or_update_google_posts( $post_id, $post, $update ) {
        if ( $post->post_status != 'publish' ) {
            return;
        }
        $this->queue_gmb_posts( $post_id );
    }

    /**
     * Check whether anything is queued for deletion and dispatch the process if required
     */
    public function should_dispatch() {
        //global $wp_actions, $wp_filter;
        if ( !$this->should_dispatch || $this->has_dispatched ) {
            return;
        }
        $this->background_process->save()->dispatch();
        $this->should_dispatch = false;
        $this->has_dispatched = true;
        //error_log(print_r($wp_actions, true));
    }

    public function before_delete_post( $post_id ) {
        $post_type = get_post_type( $post_id );
        switch ( $post_type ) {
            case SubPost::POST_TYPE:
                $this->delete_subpost( $post_id );
                break;
            case GooglePostEntity::POST_TYPE:
                $this->delete_single_entity( $post_id );
                break;
            default:
                if ( in_array( $post_type, $this->enabled_post_types ) ) {
                    $this->find_and_delete_child_posts( $post_id );
                }
        }
    }

    public function queue_gmb_posts( $post_id ) {
        $parent_id = wp_get_post_parent_id( $post_id );
        if ( !$parent_id || get_post_status( $parent_id ) == 'trash' ) {
            return;
        }
        $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
        $data = new ParseFormFields($form_fields);
        $postPublishDate = get_post_meta( $post_id, '_mbp_post_publish_date', true );
        if ( !$postPublishDate ) {
            update_post_meta( $post_id, '_mbp_post_publish_date', time() );
        }
        delete_post_meta( $post_id, 'mbp_last_error' );
        //Todo: Skip queue if its just 1 location?
        $accounts = $data->getLocations( $this->default_location );
        foreach ( $accounts as $user_key => $locations ) {
            //If its just a single location, backward compatibility
            if ( !is_array( $locations ) ) {
                $locations = [$locations];
            }
            foreach ( $locations as $location ) {
                if ( mbp_fs()->is_plan_or_trial__premium_only( 'starter' ) && $data->get_topic_type() === 'PRODUCT' ) {
                    $item = [
                        'action' => 'create_product',
                        'args'   => [
                            'for_post_id' => $post_id,
                            'location'    => $location,
                            'user_key'    => $user_key,
                        ],
                    ];
                    if ( $this->bypass_wp_cron ) {
                        $this->background_process->create_product( $post_id, $location, $user_key );
                        continue;
                    }
                    $this->background_process->push_to_queue( $item );
                    continue;
                }
                $item = [
                    'action' => 'mbp_create_google_post',
                    'args'   => [
                        'post_id'  => $post_id,
                        'location' => $location,
                        'user_key' => $user_key,
                    ],
                ];
                if ( $this->bypass_wp_cron ) {
                    $this->background_process->create_google_post( $post_id, $location, $user_key );
                    continue;
                }
                $this->background_process->push_to_queue( $item );
            }
        }
        if ( !$this->bypass_wp_cron ) {
            $this->background_process->save()->dispatch();
            $this->background_process->data( [] );
            //Clear data to prevent duplicate posts
        }
    }

    protected function find_and_delete_child_posts( $post_id ) {
        $children = get_children( [
            'post_parent' => $post_id,
            'post_type'   => SubPost::POST_TYPE,
            'fields'      => 'ids',
        ] );
        foreach ( $children as $child_id ) {
            wp_delete_post( $child_id, true );
        }
    }

    public function delete_subpost( $post_id ) {
        wp_clear_scheduled_hook( 'mbp_scheduled_google_post', [$post_id] );
        wp_clear_scheduled_hook( 'pgmb_delete_previous_post', [$post_id] );
        //Clear deletion schedule for sibling post
        $previous_post_id = (int) get_post_meta( $post_id, '_pgmb_previous_post_id', true );
        if ( $previous_post_id ) {
            wp_clear_scheduled_hook( 'pgmb_delete_previous_post', [$previous_post_id] );
        }
        $post_entities = $this->repository->find_by_parent( $post_id )->find();
        if ( empty( $post_entities ) ) {
            return;
        }
        foreach ( $post_entities as $post_entity ) {
            //$this->queue_entity_delete($post_entity);
            $this->repository->delete( $post_entity );
        }
        //$this->background_process->save()->dispatch();
    }

    protected function delete_single_entity( $post_id ) {
        if ( !$this->delete_gmb_posts ) {
            return;
        }
        $post_entity = $this->repository->find_by_id( $post_id );
        $this->queue_entity_delete( $post_entity );
        $this->should_dispatch = true;
        //$this->background_process->save()->dispatch();
    }

    protected function queue_entity_delete( $post_entity ) {
        if ( !$post_entity instanceof GooglePostEntity || empty( $post_entity->get_post_name() ) || empty( $post_entity->get_user_key() ) ) {
            return;
        }
        if ( mbp_fs()->is_plan_or_trial__premium_only( 'starter' ) && strpos( $post_entity->get_post_name(), 'products' ) !== false ) {
            $item = [
                'action' => 'delete_product',
                'args'   => [
                    'user_key'  => $post_entity->get_user_key(),
                    'post_name' => $post_entity->get_post_name(),
                ],
            ];
            if ( $this->bypass_wp_cron ) {
                $this->background_process->delete_product( $post_entity->get_user_key(), $post_entity->get_post_name() );
                return;
            }
            $this->background_process->push_to_queue( $item );
            return;
        }
        $item = [
            'action' => 'mbp_delete_gmb_post',
            'args'   => [
                'user_key'  => $post_entity->get_user_key(),
                'post_name' => $post_entity->get_post_name(),
            ],
        ];
        if ( $this->bypass_wp_cron ) {
            $this->background_process->delete_post( $post_entity->get_user_key(), $post_entity->get_post_name() );
            return;
        }
        $this->background_process->push_to_queue( $item );
    }

}
