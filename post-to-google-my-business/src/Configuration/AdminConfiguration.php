<?php

namespace PGMB\Configuration;

use PGMB\Admin\AdminPage;
use PGMB\Admin\AutoPostTemplateUpsell;
use PGMB\Admin\DashboardPage;
use PGMB\Admin\PostCampaignsUpsell;
use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\GoogleUserManager;
use PGMB\Vendor\TypistTech\WPAdminNotices\Factory;
class AdminConfiguration implements ContainerConfigurationInterface {
    public function modify( Container $container ) {
        $container['admin_notice_store'] = $container->service( function ( Container $container ) {
            return Factory::build( 'pgmb_admin_notices', 'pgmb_admin_notices' );
        } );
        $container['dashboard_page'] = $container->service( function ( Container $container ) {
            return new DashboardPage(
                $container['plugin_path'] . 'templates/admin/',
                $container['plugin_version'],
                $container['plugin_url'],
                $container['notification_manager']
            );
        } );
        $container['user_manager'] = $container->service( function ( Container $container ) {
            return new GoogleUserManager($container['proxy_auth_api'], $container['wordpress.http_transport']);
        } );
        $container['admin_pages'] = $container->service( function ( Container $container ) {
            $admin_page_args = [
                $container['wedevs_settings_api'],
                $container['plugin_version'],
                $container['component.business_selector'],
                $container['component.post_editor'],
                $container['plugin_path'] . 'templates/admin/',
                $container['plugin_url']
            ];
            $pages = [
                'main_page' => $container['dashboard_page'],
            ];
            $pages['settings_page'] = new AdminPage(...$admin_page_args);
            if ( !mbp_fs()->is_plan_or_trial( 'pro' ) ) {
                $pages['posttype_upsell_page'] = new AutoPostTemplateUpsell($container['plugin_path'] . 'templates/admin/', $container['plugin_url']);
            }
            if ( !mbp_fs()->is_plan_or_trial( 'business' ) ) {
                $pages['postcampaign_upsell_page'] = new PostCampaignsUpsell($container['plugin_path'] . 'templates/admin/', $container['plugin_url']);
            }
            return $pages;
        } );
    }

}
