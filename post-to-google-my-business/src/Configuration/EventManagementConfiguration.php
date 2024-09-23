<?php

namespace PGMB\Configuration;

use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\EventManagement\EventManager;
use PGMB\Subscriber;
use PGMB\Upgrader\UpgradeBackgroundProcess;
use PGMB\Upgrader\Upgrader;
class EventManagementConfiguration implements ContainerConfigurationInterface {
    public function modify( Container $container ) {
        $container['event_manager'] = $container->service( function ( Container $container ) {
            return new EventManager();
        } );
        $container['upgrade_background_process'] = $container->service( function ( Container $container ) {
            return new UpgradeBackgroundProcess('mbp');
        } );
        $container['subscribers'] = $container->service( function ( Container $container ) {
            $subscribers = [
                new Subscriber\AuthenticationAdminPostSubscriber($container['proxy_auth_api'], $container['user_manager']),
                new Subscriber\CalendarFeedAjaxSubscriber($container['repository.subposts']),
                new Upgrader(
                    $container['upgrade_background_process'],
                    $container['plugin_version'],
                    'mbp',
                    $container['available_upgrades']
                ),
                new Subscriber\PostStatusSubscriber(
                    $container['post_publishing_process'],
                    $container['repository.post_entities'],
                    $container['setting.default_location'],
                    $container['setting.delete_gmb_posts'],
                    $container['setting.enabled_post_types'],
                    $container['setting.bypass_wp_cron']
                ),
                new Subscriber\SubPostListAjaxSubscriber($container['repository.subposts']),
                new Subscriber\PostEntityListAjaxSubscriber($container['repository.post_entities'], $container['google_my_business_api'], $container['post_publishing_process']),
                new Subscriber\AdminPageSubscriber(
                    $container['dashboard_page'],
                    $container['admin_pages'],
                    $container['plugin_dashicon'],
                    $container['notification_manager']
                ),
                new Subscriber\ConditionalNotificationSubscriber($container['notification_manager']),
                new Subscriber\AutoPostSubscriber(
                    $container['setting.enabled_post_types'],
                    $container['setting.invert_checkbox'],
                    $container['factory.autopost_factory'],
                    $container['setting.enabled_request_types']
                ),
                new Subscriber\MetaboxSubscriber($container['metaboxes']),
                new Subscriber\BlockEditorAssetSubscriber(
                    $container['setting.enabled_post_types'],
                    $container['plugin_url'],
                    $container['plugin_version'],
                    $container['wedevs_settings_api']
                ),
                new Subscriber\PostSubmitBoxSubscriber($container['setting.enabled_post_types'], $container['setting.invert_checkbox']),
                new Subscriber\SiteHealthSubscriber(),
                new Subscriber\AdminStyleSubscriber($container['plugin_url'], $container['plugin_version'])
            ];
            $subscribers[] = new Subscriber\PostTypesSubscriber();
            return $subscribers;
        } );
    }

}
