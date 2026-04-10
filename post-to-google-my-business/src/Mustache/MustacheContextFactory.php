<?php

namespace PGMB\Mustache;


use PGMB\ApiCache\Location;
use PGMB\Mustache\Context\LocationContext;
use PGMB\Mustache\Context\PostContext;
use PGMB\Mustache\Context\SiteContext;
use PGMB\Mustache\Context\ThirdParty\AcfContext;
use PGMB\Mustache\Context\ThirdParty\WooCommerceContext;
use PGMB\Mustache\Context\UserContext;
use PGMB\Mustache\Context\CustomMetaContext;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MustacheContextFactory {

	private function generate($post_id, Location $location): MustacheContextBuilder {
		$builder = new MustacheContextBuilder();

		$providers = [
			new PostContext($post_id),
			new LocationContext($location),
			new SiteContext(),
			new UserContext($post_id),
			new CustomMetaContext($post_id),
		];

		if(function_exists('get_fields')) {
			$providers[] = new AcfContext( $post_id );
		}

		if (class_exists('WooCommerce')) {
			$providers[] = new WooCommerceContext($post_id);
		}

		$providers = apply_filters(
			'pgmb_mustache_context_providers',
			$providers,
			$post_id,
			$location
		);

		foreach($providers as $provider){
			$builder->add_provider($provider);
		}
		return $builder;
	}
	public function build_for_post($post_id, Location $location): array {
		$builder = $this->generate($post_id, $location);

		return $builder->build();
	}

	public function describe_for_post($post_id, Location $location): array {
		$builder = $this->generate($post_id, $location);

		return $builder->describe_all();
	}
}