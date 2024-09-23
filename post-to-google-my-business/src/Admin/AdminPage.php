<?php

namespace PGMB\Admin;

use PGMB\Components\BusinessSelector;
use PGMB\Components\PostEditor;
use PGMB\EventManagement\SubscriberInterface;
use PGMB\FormFields;
use PGMB\Notifications\BasicNotification;
use PGMB\Notifications\NotificationManager;
use PGMB\PostTypes\SubPost;
use PGMB\Premium\Components\MultiAccountBusinessSelector;
use PGMB\Premium\PostTypes\PostCampaign;
use PGMB\Premium\PostTypes\PostTypeAutoPostTemplate;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone;
use PGMB\Vendor\WeDevsSettingsAPI;
use PGMB\WordPressInitializable;
class AdminPage extends AbstractPage implements ConfigurablePageInterface, EnqueuesScriptsInterface, AjaxCallbackInterface {
    const POST_EDITOR_CALLBACK_PREFIX = 'mbp_settings_posteditor';

    const BUSINESSSELECTOR_CALLBACK_PREFIX = 'mbp_settings_selector';

    const FIELD_PREFIX = 'mbp_quick_post_settings[autopost_template]';

    public $settings_api;

    private $plugin_version;

    public $notification_manager;

    private $business_selector;

    private $autopost_editor;

    public function __construct(
        WeDevsSettingsAPI $settings_api,
        $plugin_version,
        $business_selector,
        $autopost_editor,
        $template_path,
        $plugin_url
    ) {
        if ( !$business_selector instanceof BusinessSelector ) {
            throw new \InvalidArgumentException('Admin page expects valid BusinessSelector component');
        }
        if ( !$autopost_editor instanceof PostEditor ) {
            throw new \InvalidArgumentException('Admin page expects valid PostEditor component');
        }
        $this->settings_api = $settings_api;
        $this->plugin_version = $plugin_version;
        $this->template_path = $template_path;
        $this->business_selector = $business_selector;
        $this->autopost_editor = $autopost_editor;
        parent::__construct( $template_path, $plugin_url );
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'jquery-ui', $this->plugin_url . 'css/jquery-ui.min.css' );
        wp_enqueue_script(
            'mbp-settings-page',
            $this->plugin_url . 'js/settings.js',
            array(
                'jquery',
                'jquery-ui-core',
                'jquery-ui-datepicker',
                'jquery-ui-slider'
            ),
            $this->plugin_version,
            true
        );
        wp_enqueue_style( 'pgmb-settings-page', $this->plugin_url . 'js/settings.css' );
        add_thickbox();
        $localize_vars = [
            'refresh_locations'                => __( 'Refresh locations', 'post-to-google-my-business' ),
            'delete_account_confirmation'      => __( 'Disconnect the Google account from this website?', 'post-to-google-my-business' ),
            'please_wait'                      => __( 'Please wait...', 'post-to-google-my-business' ),
            'wait_for_locations_to_load'       => __( 'Please wait for all locations to load', 'post-to-google-my-business' ),
            'POST_EDITOR_CALLBACK_PREFIX'      => self::POST_EDITOR_CALLBACK_PREFIX,
            'BUSINESSSELECTOR_CALLBACK_PREFIX' => self::BUSINESSSELECTOR_CALLBACK_PREFIX,
            'FIELD_PREFIX'                     => self::FIELD_PREFIX,
            'locale'                           => get_locale(),
            'nonce'                            => wp_create_nonce( 'pgmb-nonce' ),
            'disable_event_dateselector'       => $this->settings_api->get_option( 'disable_event_dateselector', 'mbp_misc' ) === 'on',
            'setting_selected_location'        => $this->settings_api->get_option( 'google_location', 'mbp_google_settings' ),
        ];
        wp_localize_script( 'mbp-settings-page', 'mbp_localize_script', $localize_vars );
    }

    function get_settings_sections() {
        return array(
            array(
                'id'    => 'mbp_google_settings',
                'title' => __( 'Google settings', 'post-to-google-my-business' ),
            ),
            array(
                'id'    => 'mbp_quick_post_settings',
                'title' => __( 'Auto-post settings', 'post-to-google-my-business' ),
            ),
            array(
                'id'    => 'mbp_post_type_settings',
                'title' => __( 'Post type settings', 'post-to-google-my-business' ),
            ),
            [
                'id'    => 'pgmb_evergreen_settings',
                'title' => __( 'Evergreen content', 'post-to-google-my-business' ),
            ],
            array(
                'id'    => 'mbp_misc',
                'title' => __( 'Misc', 'post-to-google-my-business' ),
            )
        );
    }

    function get_settings_fields() {
        return array(
            'mbp_google_settings'     => array(array(
                'name'     => 'google_location',
                'label'    => __( 'Default location', 'post-to-google-my-business' ),
                'desc'     => __( 'Select the post-types where the GMB metabox & auto-post controls should be displayed', 'post-to-google-my-business' ),
                'callback' => [$this, 'settings_field_google_business'],
            )),
            'mbp_quick_post_settings' => array(array(
                'name'  => 'invert',
                'label' => __( 'Post to GMB by default', 'post-to-google-my-business' ),
                'desc'  => __( 'The Auto-post checkbox will be checked by default, and your WordPress posts will be automatically published to GMB, unless you uncheck it.', 'post-to-google-my-business' ),
                'type'  => 'checkbox',
            ), array(
                'name'              => 'autopost_template',
                'label'             => __( 'Default template', 'post-to-google-my-business' ),
                'desc'              => sprintf( __( 'The template for new Google posts when using quick post. Supports <a target="_blank" href="%s">variables</a> and <a target="_blank" href="%s">spintax</a> (premium only)', 'post-to-google-my-business' ), 'https://tycoonmedia.net/blog/using-the-quick-publish-feature/', 'https://tycoonmedia.net/blog/using-spintax/' ),
                'callback'          => [$this, 'settings_field_autopost_template'],
                'sanitize_callback' => [$this, 'validate_autopost_template'],
                'default'           => \PGMB\FormFields::default_autopost_fields(),
            ), [
                'name'    => 'enabled_request_types',
                'label'   => __( 'Enabled request types (advanced)', 'post-to-google-my-business' ),
                'desc'    => __( 'Select which request types the plugin should listen for to create auto-posts. Do not change unless you know what you are doing. Incorrect settings could result in duplicate posts or spamming your listing with posts.', 'post-to-google-my-business' ),
                'type'    => 'multicheck',
                'options' => [
                    'editor'   => __( 'Posts/pages/CPTs created on the front-end through the Block or Classic editor', 'post-to-google-my-business' ),
                    'internal' => __( 'Internal (e.g. items created internally by 3rd party plugins like import plugins)', 'post-to-google-my-business' ),
                    'rest'     => __( 'REST API (Items created through the WP REST API)', 'post-to-google-my-business' ),
                    'xmlrpc'   => __( 'XML-RPC (Items created through XML-RPC)', 'post-to-google-my-business' ),
                ],
                'default' => array(
                    'editor' => 'editor',
                ),
            ]),
            'mbp_post_type_settings'  => [[
                'name'              => 'post_types',
                'label'             => __( 'Enabled for post types', 'post-to-google-my-business' ),
                'desc'              => __( 'Select the post-types where the GMB metabox should be displayed', 'post-to-google-my-business' ),
                'type'              => 'multicheck',
                'default'           => array(
                    'post' => 'post',
                ),
                'options'           => $this->settings_field_post_types(),
                'sanitize_callback' => array($this, 'validate_post_types__premium_only'),
            ]],
            'mbp_misc'                => [
                [
                    'name'    => 'delete_gmb_posts',
                    'label'   => __( 'Delete GMB posts', 'post-to-google-my-business' ),
                    'desc'    => __( 'Delete post(s) from GMB when the parent post/page/content in WordPress is deleted', 'post-to-google-my-business' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'uninstall_cleanup',
                    'label'   => __( 'Uninstall cleanup', 'post-to-google-my-business' ),
                    'desc'    => __( 'Choose which data the plugin should (try to) delete when uninstalling the plugin', 'post-to-google-my-business' ),
                    'type'    => 'multicheck',
                    'options' => [
                        'delete_settings'  => __( 'Delete plugin settings', 'post-to-google-my-business' ),
                        'delete_posttypes' => __( 'Delete GMB post data, post campaigns & auto-post templates', 'post-to-google-my-business' ),
                    ],
                ],
                [
                    'name'    => 'disable_event_dateselector',
                    'label'   => __( 'Disable date & time selector for events/offers', 'post-to-google-my-business' ),
                    'desc'    => __( 'Disable the event/offer date & time selector to allow for relative dates in those fields', 'post-to-google-my-business' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ],
                [
                    'name'    => 'bypass_wp_cron',
                    'label'   => __( 'Bypass WP-Cron', 'post-to-google-my-business' ),
                    'desc'    => __( 'Enable this only if your posts are not being published. Will publish posts in foreground, at expense of performance.', 'post-to-google-my-business' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ]
            ],
        );
    }

    public function is_configured() {
        $accounts = get_option( 'pgmb_accounts' );
        return sprintf( '<br /><span class="dashicons dashicons-%s"></span> %s', ( $accounts ? 'yes' : 'no' ), ( $accounts ? __( 'Connected', 'post-to-google-my-business' ) : __( 'Not connected', 'post-to-google-my-business' ) ) );
    }

    public function google_form_top() {
        echo $this->is_configured();
        echo $this->auth_urls();
        echo '<br /><br />';
    }

    public function settings_field_autopost_template( $args ) {
        $values = $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] );
        $name = sprintf( '%1$s[%2$s]', $args['section'], $args['id'] );
        //$user = $this->get_current_setting('google_user', 'mbp_google_settings');
        //Fix for default checkboxes enabled by default not saving when turned off
        $values = array_merge( FormFields::empty_post_fields(), $values );
        $this->autopost_editor->set_field_name( $name );
        $this->autopost_editor->set_values( $values );
        echo $this->autopost_editor->generate();
    }

    public function settings_field_post_types() {
        $query_args = array(
            'public' => true,
        );
        //Maybe add some additional filtering later
        $post_types = array();
        foreach ( get_post_types( $query_args, 'objects' ) as $type ) {
            $post_types[$type->name] = $type->label;
        }
        return $post_types;
    }

    public function settings_field_google_business( $args ) {
        $value = $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] );
        $name = sprintf( '%1$s[%2$s]', $args['section'], $args['id'] );
        //$user = $this->get_current_setting('google_user', 'mbp_google_settings');
        ?>
		<div class="mbp-google-settings-business-selector">
			<?php 
        $this->business_selector->set_field_name( $name );
        $this->business_selector->set_selected_locations( $value );
        echo $this->business_selector->location_blocked_info();
        echo $this->business_selector->generate();
        echo $this->business_selector->business_selector_controls();
        ?>
		</div>
		<br /><br />
		<?php 
        echo $this->message_of_the_day();
    }

    public function message_of_the_day() {
        if ( !mbp_fs()->can_use_premium_code() ) {
            $messages = [
                /*
                sprintf('%s <a target="_blank" href="%s">%s</a> %s',
                	__('Get more visitors to your website with a call-to-action button in your post.', 'post-to-google-my-business'),
                	esc_url(admin_url('options-general.php?page=my_business_post-pricing')),
                	__('Upgrade to Premium', 'post-to-google-my-business'),
                	__('for call-to-action buttons, post statistics and more.', 'post-to-google-my-business')
                )
                */
                sprintf(
                    '%s <a target="_blank" href="%s">%s</a> %s',
                    __( 'Manage multiple businesses or locations?', 'post-to-google-my-business' ),
                    mbp_fs()->get_upgrade_url(),
                    __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                    __( 'to pick a location per post, or post to multiple locations at once.', 'post-to-google-my-business' )
                ),
                sprintf(
                    '%s <a target="_blank" href="%s">%s</a> %s',
                    __( 'Not the right time?', 'post-to-google-my-business' ),
                    mbp_fs()->get_upgrade_url(),
                    __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                    __( 'and schedule your posts to be automagically published at a later time.', 'post-to-google-my-business' )
                ),
                sprintf(
                    '%s <a target="_blank" href="%s">%s</a> %s',
                    __( 'Wondering how your Google My Business post is performing?', 'post-to-google-my-business' ),
                    mbp_fs()->get_upgrade_url(),
                    __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                    __( 'to view post statistics and easily include Google Analytics UTM parameters.', 'post-to-google-my-business' )
                ),
                sprintf(
                    '%s <a target="_blank" href="%s">%s</a> %s',
                    __( 'Use Post to Google My Business for your pages, projects, WooCommerce products and more.', 'post-to-google-my-business' ),
                    mbp_fs()->get_upgrade_url(),
                    __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                    __( 'to enable Post to Google my Business for any post type.', 'post-to-google-my-business' )
                ),
                sprintf(
                    '%s <a target="_blank" href="%s">%s</a> %s',
                    __( 'Automatically repost your GMB posts a specific or unlimited amount of times.', 'post-to-google-my-business' ),
                    mbp_fs()->get_upgrade_url(),
                    __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                    __( 'to set custom intervals and specify the amount of reposts.', 'post-to-google-my-business' )
                ),
                sprintf(
                    '%s <a target="_blank" href="https://wordpress.org/plugins/post-to-google-my-business/">%s</a> %s',
                    __( 'I hope you enjoy using my Post to Google My Business plugin! Help spread the word with a', 'post-to-google-my-business' ),
                    __( '5-star rating on WordPress.org', 'post-to-google-my-business' ),
                    __( '. Many thanks! - Koen Reus, plugin developer', 'post-to-google-my-business' )
                ),
                sprintf(
                    '%s <a target="_blank" href="%s">%s</a> %s',
                    __( 'Create unique posts every time.', 'post-to-google-my-business' ),
                    mbp_fs()->get_upgrade_url(),
                    __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                    __( 'to use spintax and %variables% in your post text.', 'post-to-google-my-business' )
                ),
            ];
            //mt_srand(date('dmY'));
            $motd = mt_rand( 0, count( $messages ) - 1 );
            return '<span class="description">' . $messages[$motd] . '</span><br />';
        }
        return false;
    }

    public function auth_urls() {
        if ( !current_user_can( 'pgmb_manage_google_accounts' ) ) {
            return;
        }
        //$configured = $this->api_connected;
        $accounts = get_option( 'pgmb_accounts' );
        echo "<br /><br />";
        if ( !$accounts || empty( $accounts ) ) {
            echo sprintf( '<a href="%s" class="button-primary">%s</a>', esc_url( admin_url( 'admin-post.php?action=mbp_generate_url' ) ), esc_html__( 'Connect to Google My Business', 'post-to-google-my-business' ) );
            return;
        }
        echo sprintf( '<a title="%s" href="#TB_inline?width=500&height=300&inlineId=multi-account-upgrade-notification" class="thickbox button button-primary">%s</a>', esc_html__( 'Multi-account support is a Post to Google My Business Agency Feature', 'post-to-google-my-business' ), esc_html__( '+ Add another Google account', 'post-to-google-my-business' ) );
        if ( !mbp_fs()->is_plan_or_trial( 'business' ) ) {
            echo sprintf( '<br /><br /><a href="%s" class="button button-secondary pgmb-disconnect-website">%s</a>', esc_url( admin_url( 'admin-post.php?action=mbp_disconnect' ) ), esc_html__( 'Disconnect Google account from this website', 'post-to-google-my-business' ) );
        } else {
            echo sprintf( '<br /><br /><a href="%s" class="button button-secondary pgmb-disconnect-website">%s</a>', esc_url( admin_url( 'admin-post.php?action=mbp_disconnect' ) ), esc_html__( 'Disconnect all Google accounts from this website', 'post-to-google-my-business' ) );
        }
    }

    public function get_business_selector() {
        return $this->business_selector;
    }

    public function get_autopost_editor() {
        return $this->autopost_editor;
    }

    public function validate_autopost_template( $value ) {
        //print_r($value);
        //error_log($value);
        return $value;
    }

    public function get_menu_slug() {
        return 'pgmb_settings';
    }

    public function get_page_title() {
        return __( 'Post to Google My Business settings', 'post-to-google-my-business' );
    }

    public function get_menu_title() {
        return __( 'Settings', 'post-to-google-my-business' );
    }

    public function render_page() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        include $this->template_path . 'settings.php';
    }

    public function get_position() {
        return 3;
    }

    public function evergreen_promotional() {
        ?>
        <h4><?php 
        esc_html_e( 'Evergreen content is a Post to Google My Business Pro feature', 'post-to-google-my-business' );
        ?></h4>
        <img src="<?php 
        echo $this->plugin_url;
        ?>/img/promotional/evergreen_demo.gif" style="float:right;" alt="<?php 
        esc_attr_e( 'Evergreen content screenshot', 'post-to-google-my-business' );
        ?>" />
        <p><?php 
        esc_html_e( 'Define a posting schedule, select your existing content in bulk, and the plugin will automatically keep your GMB listing(s) fresh with new posts.', 'post-to-google-my-business' );
        ?></p>
        <p><strong><?php 
        esc_html_e( 'Post to Google my Business Pro lets you:', 'post-to-google-my-business' );
        ?></strong></p>
        <ul>
            <li><?php 
        esc_html_e( 'Create multiple auto-post templates', 'post-to-google-my-business' );
        ?></li>
            <li><?php 
        esc_html_e( 'Publish posts to multiple GMB locations at once', 'post-to-google-my-business' );
        ?></li>
            <li><?php 
        esc_html_e( 'Automatically re-cycle your posts', 'post-to-google-my-business' );
        ?></li>
            <li><?php 
        esc_html_e( 'Publish posts to multiple locations across multiple Google accounts at once', 'post-to-google-my-business' );
        ?></li>
        </ul>
        <br />
        <a class="button button-primary" href="<?php 
        echo mbp_fs()->get_upgrade_url();
        ?>"><?php 
        _e( 'View pricing &amp; buy now &raquo;', 'post-to-google-my-business' );
        ?></a>

        <?php 
    }

    public function configure() {
        $this->autopost_editor->register_ajax_callbacks( self::POST_EDITOR_CALLBACK_PREFIX );
        $this->business_selector->register_ajax_callbacks( self::BUSINESSSELECTOR_CALLBACK_PREFIX );
        $this->business_selector->set_field_name( 'mbp_google_settings[google_location]' );
        $selected_locations = $this->settings_api->get_option( 'google_location', 'mbp_google_settings' );
        $this->business_selector->set_selected_locations( $selected_locations );
        if ( mbp_fs()->is_plan_or_trial__premium_only( 'starter' ) && $this->business_selector instanceof MultiAccountBusinessSelector ) {
            $this->business_selector->enable_account_cookie_control( true );
        }
        if ( mbp_fs()->is_plan_or_trial__premium_only( 'business' ) && $this->business_selector instanceof MultiAccountBusinessSelector ) {
            $this->business_selector->enable_account_delete_control( true );
        }
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
        $this->settings_api->admin_init();
        add_filter( 'wsa_form_top_mbp_google_settings', [$this, 'google_form_top'] );
        if ( !mbp_fs()->is_plan_or_trial( 'pro' ) ) {
            add_filter( 'wsa_form_bottom_pgmb_evergreen_settings', [$this, 'evergreen_promotional'] );
        }
    }

    public function ajax_callbacks() {
        return [];
    }

}
