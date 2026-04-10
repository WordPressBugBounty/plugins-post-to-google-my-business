<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\SubscriberInterface;
class BlockEditorAssetSubscriber implements SubscriberInterface {
    private $enabled_post_types;

    private $plugin_url;

    private $settings_api;

    private $plugin_path;

    public static function get_subscribed_hooks() {
        return [
            'enqueue_block_editor_assets' => 'enqueue_block_editor_assets',
        ];
    }

    public function __construct(
        $enabled_post_types,
        $plugin_url,
        $plugin_path,
        $settings_api
    ) {
        $this->enabled_post_types = $enabled_post_types;
        $this->plugin_url = $plugin_url;
        $this->settings_api = $settings_api;
        $this->plugin_path = $plugin_path;
    }

    /**
     * Enqueue assets for the Block Editor
     *
     * @return void
     */
    public function enqueue_block_editor_assets() {
        $script_assets = (require $this->plugin_path . 'js/block_editor.asset.php');
        /*
         * Not sure why this method of selective loading isn't used in enqueue_metabox_assets(), take care...
         */
        $post_type = get_post_type();
        if ( !in_array( $post_type, $this->enabled_post_types ) || !post_type_supports( $post_type, 'custom-fields' ) ) {
            return;
        }
        wp_enqueue_script(
            'pgmb-block-editor',
            $this->plugin_url . 'js/block_editor.js',
            $script_assets['dependencies'],
            $script_assets['version'],
            true
        );
        wp_localize_script( 'pgmb-block-editor', 'pgmb_block_editor_data', [
            'checkedByDefault' => $this->settings_api->get_option( 'invert', 'mbp_quick_post_settings', 'off' ) == 'on',
        ] );
    }

}
