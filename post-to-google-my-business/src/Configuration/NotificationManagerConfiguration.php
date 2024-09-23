<?php


namespace PGMB\Configuration;



use PGMB\DependencyInjection\Container;
use PGMB\DependencyInjection\ContainerConfigurationInterface;
use PGMB\Notifications\NotificationManager;

class NotificationManagerConfiguration implements ContainerConfigurationInterface {

	public function modify( Container $container ) {
		$container['notification_manager'] = $container->service(function(Container $container){
			return new NotificationManager('pgmb');
		});
	}
}
