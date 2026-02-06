<?php

namespace PGMB\Metabox;

use DateTime;
use Exception;
use InvalidArgumentException;
use PGMB\Admin\AjaxCallbackInterface;
use PGMB\API\APIInterface;
use PGMB\API\CachedGoogleMyBusiness;
use PGMB\ApiCache\LocationCacheRepository;
use PGMB\Components\PostEditor;
use PGMB\EventManagement\SubscriberInterface;
use PGMB\Google\NormalizeLocationName;
use PGMB\PostTypes\GooglePostEntityRepository;
use PGMB\PostTypes\SubPost;
use PGMB\Premium\PostTypes\PostCampaign;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTime;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeInterface;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone;
use PGMB\Vendor\WeDevsSettingsAPI;
use PGMB\WordPressInitializable;
use WP_REST_Request;
use WP_Screen;
class PostCreationMetabox implements JSMetaboxInterface, AjaxCallbackInterface {
    const AJAX_CALLBACK_PREFIX = 'mbp_metabox';

    private $plugin_version;

    private $post_editor;

    private $enabled_post_types;

    /**
     * @var WeDevsSettingsAPI
     */
    private $settings_api;

    private $plugin_path;

    private $plugin_url;

    /**
     * @var bool Store whether the
     */
    private $is_gutenberg_autopost;

    /**
     * @var APIInterface
     */
    private $location_repository;

    public function __construct(
        WeDevsSettingsAPI $settings_api,
        LocationCacheRepository $location_repository,
        $plugin_version,
        $post_editor,
        $enabled_post_types,
        $plugin_path,
        $plugin_url
    ) {
        if ( !$post_editor instanceof PostEditor ) {
            throw new InvalidArgumentException('PostCreationMetabox metabox expects PostEditor Component');
        }
        $this->plugin_version = $plugin_version;
        $this->post_editor = $post_editor;
        $this->enabled_post_types = $enabled_post_types;
        $this->settings_api = $settings_api;
        $this->plugin_path = $plugin_path;
        $this->plugin_url = $plugin_url;
        $this->location_repository = $location_repository;
    }

    public static function get_subscribed_hooks() {
        return [
            'wp_ajax_mbp_new_post'               => 'ajax_create_post',
            'wp_ajax_mbp_load_post'              => 'ajax_load_post',
            'wp_ajax_mbp_delete_post'            => 'ajax_delete_post',
            'wp_ajax_mbp_edit_post'              => 'ajax_edit_post',
            'wp_ajax_mbp_load_autopost_template' => 'ajax_load_autopost_template',
            'wp_ajax_mbp_get_post_rows'          => 'ajax_get_post_rows',
            'wp_ajax_mbp_get_created_posts'      => 'ajax_created_posts_list',
            'init'                               => 'register_gutenberg_meta',
        ];
    }

    public function admin_init() {
        $this->post_editor->register_ajax_callbacks( self::AJAX_CALLBACK_PREFIX );
        $this->post_editor->set_ajax_enabled( true );
    }

    /**
     * Register the autopost field with Gutenberg
     *
     * @return void
     */
    public function register_gutenberg_meta() {
        //		foreach($this->enabled_post_types as $post_type) {
        //		    //Check if the post type supports custom fields or the Block editor will throw an error
        //		    if(!post_type_supports($post_type, 'custom-fields')){
        //		        continue;
        //            }
        //			add_filter("rest_pre_insert_{$post_type}", [$this, 'catch_rest_request'], 10, 2);
        //		}
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

    /**
     * Enqueue the main frontend assets for the metabox
     *
     * @param $hook
     */
    public function enqueue_scripts( $hook ) {
        wp_enqueue_style( 'jquery-ui', $this->plugin_url . 'css/jquery-ui.min.css' );
        wp_enqueue_media();
        add_thickbox();
        $localize_vars = [
            'nonce'                      => wp_create_nonce( 'pgmb-nonce' ),
            'locale'                     => get_locale(),
            'post_id'                    => get_the_ID(),
            'post_nonce'                 => wp_create_nonce( 'mbp_post_nonce' ),
            'AJAX_CALLBACK_PREFIX'       => self::AJAX_CALLBACK_PREFIX,
            'POST_EDITOR_DEFAULT_FIELDS' => \PGMB\FormFields::default_post_fields(),
            'disable_event_dateselector' => $this->settings_api->get_option( 'disable_event_dateselector', 'mbp_misc' ) === 'on',
        ];
        wp_enqueue_script(
            'mbp-metabox',
            $this->plugin_url . 'js/metabox.js',
            array(
                'jquery',
                'jquery-ui-core',
                'jquery-ui-datepicker',
                'jquery-ui-slider',
                'wp-hooks',
                'wp-i18n'
            ),
            $this->plugin_version,
            true
        );
        wp_enqueue_style( 'pgmb-metabox', $this->plugin_url . 'js/metabox.css' );
        wp_localize_script( 'mbp-metabox', 'mbp_localize_script', $localize_vars );
        wp_set_script_translations( 'mbp-metabox', 'post-to-google-my-business', $this->plugin_path . 'languages' );
    }

    /**
     * Render the metabox
     *
     * @return void
     */
    public function render_meta_box( \WP_Post $post ) {
        if ( $this->settings_api->get_option( 'google_location', 'mbp_google_settings' ) ) {
            require_once $this->plugin_path . 'templates/metabox.php';
        } else {
            echo sprintf( '<a href="%s">', esc_url( admin_url( 'admin.php?page=pgmb_settings#mbp_google_settings' ) ) );
            _e( 'Please configure Post to Google My Business first', 'post-to-google-my-business' );
            echo '</a> ';
            _e( '(Connect, pick a default location and Save Changes)', 'post-to-google-my-business' );
        }
    }

    /**
     * Determine whether the auto-post features should be enabled on the metabox
     *
     * @return bool Autopost is enabled
     * @since 2.2.11
     */
    public function is_autopost_enabled() {
        return true;
    }

    /**
     * Sanitize the form fields
     *
     * @param array $fields - Array containing the form fields
     *
     * @param array $textarea_fields - Fields that should be sanitized as textarea
     *
     * @param array $ignored_fields
     *
     * @return array - Sanitized form fields
     */
    public function sanitize_form_fields( $fields, $textarea_fields = [], $ignored_fields = [] ) {
        foreach ( $fields as $name => $value ) {
            if ( in_array( $name, $ignored_fields ) ) {
                continue;
            }
            if ( is_array( $value ) ) {
                $fields[$name] = array_map( 'sanitize_text_field', $value );
                continue;
            }
            if ( in_array( $name, $textarea_fields ) ) {
                $fields[$name] = sanitize_textarea_field( $value );
                continue;
            }
            $fields[$name] = sanitize_text_field( $value );
        }
        return $fields;
    }

    /**
     * @param $parent_post_id
     * @param $fields
     *
     * @throws Exception Fields did not validate
     */
    public function validate_form_fields( $parent_post_id, $fields ) {
        $parsed_fields = new \PGMB\ParseFormFields($fields);
        $default_location = (array) $this->settings_api->get_option( 'google_location', 'mbp_google_settings' );
        $location_name = reset( $default_location );
        $location = $this->location_repository->get_location_by_google_id( NormalizeLocationName::from_with_account( $location_name )->without_account_id() );
        if ( mbp_fs()->is__premium_only() && $parsed_fields->get_topic_type() === 'PRODUCT' ) {
            $parsed_fields->get_product__premium_only( $location, $parent_post_id );
            return;
        }
        $parsed_fields->getLocalPost( $location, $parent_post_id );
    }

    public function wp_time_format() {
        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );
        return "{$date_format} {$time_format}";
    }

    /**
     * Handle AJAX post submission
     */
    public function ajax_create_post() {
        check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
        $parent_post_id = intval( $_POST['mbp_post_id'] );
        if ( !current_user_can( 'publish_posts', $parent_post_id ) ) {
            wp_send_json_error( array(
                'error' => __( 'You do not have permission to publish posts', 'post-to-google-my-business' ),
            ) );
        }
        $editing = $child_post_id = ( isset( $_POST['mbp_editing'] ) && is_numeric( $_POST['mbp_editing'] ) ? intval( $_POST['mbp_editing'] ) : false );
        $draft = isset( $_POST['mbp_draft'] ) && json_decode( $_POST['mbp_draft'] );
        $data_mode = sanitize_text_field( $_POST['mbp_data_mode'] );
        //$form_fields = $this->sanitize_form_fields($_POST['mbp_form_fields'], ['mbp_post_text']);
        parse_str( $_POST['mbp_serialized_fieldset'], $parsed_fieldset );
        //$form_fields = $this->sanitize_form_fields($parsed_fieldset['mbp_form_fields'], ['mbp_post_text'], ['mbp_selected_location', 'mbp_button_url', 'mbp_offer_redeemlink', 'mbp_post_attachment']);
        $parsed_form_fields = new \PGMB\ParseFormFields($parsed_fieldset['mbp_form_fields']);
        $form_fields = $parsed_form_fields->sanitize();
        $json_args = [];
        switch ( $data_mode ) {
            case "save_draft":
            case "edit_post":
            case "create_post":
                $subpost = SubPost::create( $parent_post_id );
                if ( $editing ) {
                    $subpost->set_editing( $child_post_id );
                }
                $subpost->set_form_fields( $form_fields );
                $subpost->set_draft( $draft );
                try {
                    $this->validate_form_fields( $parent_post_id, $form_fields );
                    $child_post_id = wp_insert_post( $subpost->get_post_data(), true );
                } catch ( \Throwable $e ) {
                    /* translators: %s represents error message */
                    wp_send_json_error( array(
                        'error' => sprintf( __( 'Error creating post: %s', 'post-to-google-my-business' ), $e->getMessage() ),
                    ) );
                }
                //				if($draft){
                //					$scheduled_date = null;
                //				}else{
                //					$scheduled_date = $parsed_form_fields->getPublishDateTime();
                //					if(!$scheduled_date instanceof WpDateTimeInterface){
                //						$scheduled_date = new WpDateTime('now', WpDateTimeZone::getWpTimezone());
                //					}
                //				}
                $json_args = array(
                    'id' => $child_post_id,
                );
                break;
            case "edit_template":
                update_post_meta( $parent_post_id, '_mbp_autopost_template', $form_fields );
                $json_args = [
                    'message' => __( 'Auto-post template successfully updated', 'post-to-google-my-business' ),
                ];
                break;
        }
        wp_send_json_success( $json_args );
    }

    public function ajax_load_post() {
        check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
        $post_id = (int) $_POST['mbp_post_id'];
        if ( !current_user_can( 'edit_posts', $post_id ) ) {
            wp_send_json( array(
                'error' => __( 'You do not have permission to edit posts', 'post-to-google-my-business' ),
            ) );
        }
        $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
        $has_error = get_post_meta( $post_id, 'mbp_last_error', true );
        if ( $form_fields && is_array( $form_fields ) ) {
            wp_send_json( array(
                'success'   => true,
                'post'      => array(
                    'form_fields' => $form_fields,
                    'post_status' => get_post_status( $post_id ),
                ),
                'has_error' => $has_error,
            ) );
        } else {
            wp_send_json( array(
                'error' => __( 'Post could not be loaded', 'post-to-google-my-business' ),
            ) );
        }
    }

    public function ajax_delete_post() {
        check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
        $post_id = (int) $_POST['mbp_post_id'];
        if ( !current_user_can( 'delete_posts', $post_id ) ) {
            wp_send_json( array(
                'error' => __( 'You do not have permission to delete posts', 'post-to-google-my-business' ),
            ) );
        }
        wp_delete_post( $post_id );
        wp_send_json_success();
    }

    public function ajax_load_autopost_template() {
        check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
        if ( empty( $_POST['mbp_post_id'] ) ) {
            wp_send_json_error( [
                'error' => __( 'Invalid post ID', 'post-to-google-my-business' ),
            ] );
        }
        $post_id = intval( $_POST['mbp_post_id'] );
        if ( $fields = get_post_meta( $post_id, '_mbp_autopost_template', true ) ) {
            wp_send_json_success( [
                'fields' => $fields,
            ] );
        }
        $template = $this->settings_api->get_option( 'autopost_template', 'mbp_quick_post_settings', \PGMB\FormFields::default_autopost_fields() );
        if ( empty( $template ) ) {
            $template = \PGMB\FormFields::default_autopost_fields();
        }
        wp_send_json_success( [
            'fields' => $template,
        ] );
    }

    /**
     * @return PostEditor
     */
    public function get_post_editor() {
        return $this->post_editor;
    }

    public function get_id() {
        return 'pgmb_post_creation_metabox';
    }

    public function get_title() {
        return __( 'Post to Google My Business', 'post-to-google-my-business' );
    }

    public function get_screen() {
        return $this->enabled_post_types;
    }

    public function ajax_callbacks() {
        return [
            'new_post'               => [$this, 'ajax_create_post'],
            'load_post'              => [$this, 'ajax_load_post'],
            'delete_post'            => [$this, 'ajax_delete_post'],
            'edit_post'              => [$this, 'ajax_edit_post'],
            'load_autopost_template' => [$this, 'ajax_load_autopost_template'],
            'get_post_rows'          => [$this, 'ajax_get_post_rows'],
            'get_created_posts'      => [$this, 'ajax_created_posts_list'],
        ];
    }

}
