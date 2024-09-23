<?php


namespace PGMB\Configuration;

use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\PostTypes\AutoPostFactory;
use PGMB\PostTypes\GooglePostEntityRepository;
use PGMB\PostTypes\SubPostRepository;


class PostTypeConfiguration implements ContainerConfigurationInterface {

	public function modify( Container $container ) {

		$container['repository.post_entities'] = $container->service(function(Container $container){
			return new GooglePostEntityRepository(new \WP_Query());
		});

		$container['repository.subposts'] = $container->service(function(Container $container){
			return new SubPostRepository(new \WP_Query());
		});

		$container['factory.autopost_factory'] = $container->service(function(Container $container){
			return new AutoPostFactory($container['setting.default_autopost_template']);
		});

	}
}
