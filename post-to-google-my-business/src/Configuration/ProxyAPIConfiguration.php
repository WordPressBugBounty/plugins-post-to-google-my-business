<?php

namespace PGMB\Configuration;

use PGMB\API\CachedGoogleMyBusiness;
use PGMB\API\ProxyAuthenticationAPI;
use PGMB\ApiCache\GroupCacheRepository;
use PGMB\ApiCache\LocationCacheRepository;
use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\Premium\API\GMBCookieAPI;
class ProxyAPIConfiguration implements ContainerConfigurationInterface {
    public function modify( Container $container ) {
        //		$container['api_token'] = get_option('mbp_api_token');
        $container['wordpress.http_transport'] = _wp_http_get_object();
        //		$container['proxy_google_api'] = $container->service(function(Container $container){
        //			return new ProxyAPI($container['wordpress.http_transport'], $container['plugin_version'], $container['site_key'], $container['api_token']);
        //		});
        $container['proxy_auth_api'] = $container->service( function ( Container $container ) {
            return new ProxyAuthenticationAPI($container['wordpress.http_transport'], $container['plugin_version']);
        } );
        $container['google_my_business_api'] = $container->service( function ( Container $container ) {
            return new CachedGoogleMyBusiness($container['wordpress.http_transport'], $container['proxy_auth_api']);
        } );
        //Todo: seperate database configuration file?
        $container['repository.group_cache'] = $container->service( function ( Container $container ) {
            global $wpdb;
            return new GroupCacheRepository($wpdb);
        } );
        $container['repository.location_cache'] = $container->service( function ( Container $container ) {
            global $wpdb;
            return new LocationCacheRepository($wpdb);
        } );
    }

}
