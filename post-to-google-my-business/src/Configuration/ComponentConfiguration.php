<?php

namespace PGMB\Configuration;

use PGMB\Components\BusinessSelector;
use PGMB\Components\PostEditor;
use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
class ComponentConfiguration implements ContainerConfigurationInterface {
    public function modify( Container $container ) {
        $container['component.business_selector'] = function ( Container $container ) {
            return new BusinessSelector(
                $container['google_my_business_api'],
                $container['service.location_sync_process'],
                $container['repository.group_cache'],
                $container['repository.location_cache']
            );
        };
        $container['component.post_editor'] = function ( Container $container ) {
            return new PostEditor($container['plugin_path'] . 'templates/admin/');
        };
    }

}
