<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\EventManager;
use PGMB\EventManagement\EventManagerAwareSubscriberInterface;
use PGMB\PostTypes\AutoPostFactory;
use PGMB\Premium\PostTypes\PostCampaign;
use WP_Post;
use WP_REST_Request;
class AutoPostSubscriber extends PostSubmitBoxSubscriber implements EventManagerAwareSubscriberInterface {
    private $enabled_post_types;

    /**
     * @var EventManager
     */
    private $event_manager;

    /**
     * @var mixed|null
     */
    private $created_through_block_editor;

    /**
     * @var AutoPostFactory
     */
    private $auto_post_factory;

    private $posts_created_in_session = [];

    private $rest_enabled = true;

    private $internal_enabled = true;

    private $xmlrpc_enabled = true;

    private $request_type;

    private $block_editor_checkbox_checked;

    private $enabled_request_types;

    public function __construct(
        $enabled_post_types,
        $invert_checkbox,
        AutoPostFactory $auto_post_factory,
        $enabled_request_types
    ) {
        $this->enabled_post_types = $enabled_post_types;
        parent::__construct( $enabled_post_types, $invert_checkbox );
        $this->auto_post_factory = $auto_post_factory;
        $this->enabled_request_types = (array) $enabled_request_types;
    }

    public static function get_subscribed_hooks() {
        return [
            'init'                   => 'init',
            'save_post'              => ['handle_autopost_on_post_save', 50, 3],
            'transition_post_status' => ['publish_scheduled_autopost', 10, 3],
        ];
    }

    public function init() {
        $this->register_meta();
        $this->hook_rest_pre_insert();
    }

    public function register_meta() {
        register_meta( 'post', '_mbp_gutenberg_autopost', [
            'show_in_rest'      => true,
            'type'              => 'boolean',
            'single'            => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
            'auth_callback'     => function () {
                return current_user_can( 'edit_posts' );
            },
        ] );
    }

    public function hook_rest_pre_insert() {
        foreach ( $this->enabled_post_types as $post_type ) {
            //Check if the post type supports custom fields or the Block editor will throw an error
            if ( !post_type_supports( $post_type, 'custom-fields' ) ) {
                continue;
            }
            $this->event_manager->add_callback(
                "rest_pre_insert_{$post_type}",
                [$this, 'catch_rest_request'],
                10,
                2
            );
            $this->event_manager->add_callback(
                "rest_after_insert_{$post_type}",
                [$this, 'after_rest_request'],
                10,
                3
            );
        }
    }

    public function after_rest_request( WP_Post $post, WP_REST_Request $request, $creating ) {
        if ( wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
            return;
        }
        if ( $this->should_create_autopost( $post->ID ) ) {
            $this->dispatch_autopost( $post );
        }
    }

    /**
     * Determine if the post was saved through the block editor
     * and whether we need to create a post
     *
     * @param $prepared_post
     * @param WP_REST_Request $request
     *
     * @return mixed
     */
    public function catch_rest_request( $prepared_post, WP_REST_Request $request ) {
        $this->created_through_block_editor = $request->get_param( "isGutenbergPost" );
        $this->block_editor_checkbox_checked = isset( $request->get_param( 'meta' )['_mbp_gutenberg_autopost'] ) && $request->get_param( 'meta' )['_mbp_gutenberg_autopost'];
        return $prepared_post;
    }

    /**
     * Determine whether we need to create an automatic post upon saving of the WP post
     *
     * @param $post_id
     * @param $post
     * @param $update
     *
     * @return void Autopost successfully created
     */
    public function handle_autopost_on_post_save( $post_id, $post, $update ) {
        //XMLRPC_REQUEST = true
        //REST_REQUEST = true
        $lock_transient_name = "pgmb_auto_post_lock_{$post->ID}";
        //		$this->posts_created_in_session[] = [
        //			'id' => $post_id,
        //			'rest'  => defined('XMLRPC_REQUEST'),
        //		];
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || !in_array( $post->post_type, $this->enabled_post_types ) || get_transient( $lock_transient_name ) || defined( 'REST_REQUEST' ) && REST_REQUEST || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }
        if ( $this->should_create_autopost( $post_id ) ) {
            $this->dispatch_autopost( $post );
        }
        //if(did_action("save_post_{$post->post_type}") > 1){ return false; }
    }

    public function dispatch_autopost( $post ) {
        $lock_transient_name = "pgmb_auto_post_lock_{$post->ID}";
        if ( $post->post_status === 'publish' ) {
            set_transient( $lock_transient_name, true, 20 );
            return $this->auto_post_factory->create_autopost( $post->ID );
        } elseif ( $post->post_status === 'future' ) {
            set_transient( $lock_transient_name, true, 20 );
            update_post_meta( $post->ID, '_pgmb_scheduled_autopost', true );
            return true;
        }
        return false;
    }

    /**
     * Check if the post has a term that has auto-post enabled
     *
     * @param $post_id
     *
     * @return bool Post has term that has auto-post enabled
     * @since 2.2.11
     */
    public function has_autopost_term( $post_id ) {
        return false;
    }

    public function publish_scheduled_autopost( $new_status, $old_status, $post ) {
        if ( $old_status === 'future' && $new_status == 'publish' && get_post_meta( $post->ID, '_pgmb_scheduled_autopost', true ) ) {
            //$this->publish_autopost($post->ID);
            $this->auto_post_factory->create_autopost( $post->ID );
            delete_post_meta( $post->ID, '_pgmb_scheduled_autopost' );
        }
    }

    public function determine_request_type() {
        if ( defined( 'REST_REQUEST' ) && $this->created_through_block_editor ) {
            $this->request_type = 'blockeditor';
        } elseif ( defined( 'REST_REQUEST' ) && !$this->created_through_block_editor ) {
            $this->request_type = 'rest';
        } elseif ( defined( 'XMLRPC_REQUEST' ) ) {
            $this->request_type = 'xmlrpc';
        } elseif ( $this->is_wp_post_submission() ) {
            $this->request_type = 'classiceditor';
        } else {
            $this->request_type = 'internal';
        }
    }

    public function should_create_autopost( $post_id ) {
        $this->determine_request_type();
        $alreadyPublished = get_post_meta( $post_id, 'mbp_autopost_created', true );
        $hasAutopostTerm = $this->has_autopost_term( $post_id );
        if ( $this->request_type == 'blockeditor' ) {
            return in_array( 'editor', $this->enabled_request_types ) && (get_post_meta( $post_id, '_mbp_gutenberg_autopost', true ) || $hasAutopostTerm && !$alreadyPublished);
        } elseif ( $this->request_type == 'classiceditor' ) {
            return in_array( 'editor', $this->enabled_request_types ) && ($this->is_autopost_checkbox_checked( $post_id ) || $hasAutopostTerm && !$alreadyPublished);
        } elseif ( $this->request_type == 'rest' || $this->request_type == 'xmlrpc' || $this->request_type == 'internal' ) {
            return in_array( $this->request_type, $this->enabled_request_types ) && !$alreadyPublished && ($this->invert_checkbox || $hasAutopostTerm);
        }
        return false;
    }

    //	/**
    //	 * Check if an autopost has to be created for this post
    //	 *
    //	 * @param $post_id
    //	 *
    //	 * @return bool Autopost should be created
    //	 * @since 2.2.11
    //	 */
    //	public function should_create_autopost($post_id){
    //		//Check if the post was submitted through the editor
    //		$savedThroughEditor = $this->is_wp_post_submission();
    //		//Check if the default behaviour is to post
    //		$checkedByDefault = $this->invert_checkbox;
    //		//Check if the checkbox was checked on the form
    //		$checkboxChecked = $this->is_autopost_checkbox_checked($post_id);
    //		//Check if the post has been published before
    //		$alreadyPublished = get_post_meta($post_id, 'mbp_autopost_created', true);
    //		//Check if the post has a term that has auto-post enabled
    //		$hasAutopostTerm = $this->has_autopost_term($post_id);
    //
    //		if($savedThroughEditor && ($checkboxChecked || ($hasAutopostTerm && !$alreadyPublished))){
    //			//If the post was created through the editor, and if the checkbox was checked, or has a term with autopost enabled
    //			return true;
    //
    //		}elseif(!$savedThroughEditor && !$alreadyPublished && ($checkedByDefault || $hasAutopostTerm)){
    //			//Post was not created through the editor, and hasn't already been posted
    //			//Check if the checkbox is checked by default, or the post has a term with autopost enabled
    //			return true;
    //		}
    //		return false;
    //	}
    public function set_event_manager( EventManager $event_manager ) {
        $this->event_manager = $event_manager;
    }

}
