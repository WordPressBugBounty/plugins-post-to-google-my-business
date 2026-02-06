<?php

namespace PGMB\Configuration;

use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\FormFields;
use PGMB\Vendor\WeDevsSettingsAPI;
class SettingsConfiguration implements ContainerConfigurationInterface {
    public function modify( Container $container ) {
        $container['wedevs_settings_api'] = $container->service( function ( Container $container ) {
            return new WeDevsSettingsAPI();
        } );
        $container['setting.default_location'] = function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'google_location', 'mbp_google_settings', false );
        };
        $container['setting.invert_checkbox'] = function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'invert', 'mbp_quick_post_settings', 'off' ) === 'on';
        };
        $container['setting.default_autopost_template'] = function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'autopost_template', 'mbp_quick_post_settings', FormFields::default_autopost_fields() );
        };
        $container['setting.enabled_request_types'] = function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'enabled_request_types', 'mbp_quick_post_settings', [
                'editor' => 'editor',
            ] );
        };
        $container['setting.delete_gmb_posts'] = $container->service( function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'delete_gmb_posts', 'mbp_misc', 'on' ) === 'on';
        } );
        $container['setting.bypass_wp_cron'] = $container->service( function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'bypass_wp_cron', 'mbp_misc', 'on' ) === 'on';
        } );
        $container['setting.enable_alert_post_type'] = $container->service( function ( Container $container ) {
            return $container['wedevs_settings_api']->get_option( 'enable_alert_post_type', 'mbp_misc', 'off' ) === 'on';
        } );
        $container['setting.enabled_post_types'] = $container->service( function ( Container $container ) {
            $enabled_post_types = array_values( (array) $container['wedevs_settings_api']->get_option( 'post_types', 'mbp_post_type_settings', ['post'] ) );
            return apply_filters( 'mbp_post_types', $enabled_post_types );
        } );
    }

}
