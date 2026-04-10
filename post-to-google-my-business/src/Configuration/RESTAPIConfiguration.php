<?php

namespace PGMB\Configuration;

use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\REST\GetAccountsRoute;
use PGMB\REST\GetPostVariables;
use PGMB\REST\PreviewPostRoute;

class RESTAPIConfiguration implements ContainerConfigurationInterface {

	public function modify( Container $container ) {
		$container['rest_routes'] = $container->service(function($container){
			return [
//				new GetAccountsRoute(),
				new GetPostVariables($container['factory.mustache_context'], $container['repository.location_cache']),
				new PreviewPostRoute($container['service.form_field_parser'], $container['repository.location_cache'], $container['setting.default_location']),
			];
		});
	}
}