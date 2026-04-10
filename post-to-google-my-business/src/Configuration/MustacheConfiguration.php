<?php

namespace PGMB\Configuration;


use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\Mustache\MustacheContextFactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MustacheConfiguration implements ContainerConfigurationInterface {

	public function modify( Container $container ) {
		$container['factory.mustache_context'] = $container->service(function(Container $container){
			return new MustacheContextFactory();
		});
	}
}