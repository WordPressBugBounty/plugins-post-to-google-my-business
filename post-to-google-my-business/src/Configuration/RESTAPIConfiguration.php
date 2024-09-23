<?php

namespace PGMB\Configuration;

use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\REST\GetAccountsRoute;

class RESTAPIConfiguration implements ContainerConfigurationInterface {

	public function modify( Container $container ) {
		$container['rest_routes'] = $container->service(function(){
			return [
				new GetAccountsRoute(),

			];
		});
	}
}