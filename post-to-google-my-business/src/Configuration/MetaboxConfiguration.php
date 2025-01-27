<?php

namespace PGMB\Configuration;

use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\Metabox\PostCreationMetabox;
class MetaboxConfiguration implements ContainerConfigurationInterface {
    public function modify( Container $container ) {
        $container['metaboxes'] = $container->service( function ( Container $container ) {
            $metaboxes = [new PostCreationMetabox(
                $container['wedevs_settings_api'],
                $container['repository.location_cache'],
                $container['plugin_version'],
                $container['component.post_editor'],
                $container['setting.enabled_post_types'],
                $container['plugin_path'],
                $container['plugin_url']
            )];
            return $metaboxes;
        } );
    }

}
