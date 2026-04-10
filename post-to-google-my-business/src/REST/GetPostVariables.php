<?php

namespace PGMB\REST;

use PGMB\ApiCache\Location;
use PGMB\ApiCache\LocationCacheRepository;
use PGMB\Mustache\MustacheContextFactory;
use PGMB\Util\MbString;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GetPostVariables implements RouteInterface {

	private MustacheContextFactory $mustache_context_factory;
	private LocationCacheRepository $location_cache_repository;

	public function __construct(MustacheContextFactory $mustache_context_factory, LocationCacheRepository $location_cache_repository) {
		$this->mustache_context_factory = $mustache_context_factory;
		$this->location_cache_repository = $location_cache_repository;
	}

	public function get_arguments(): array {
		return [
			'post_id' => [
				'type' => 'integer',
				'required' => false,
				'sanitize_callback' => function($value){
					return (int)$value;
				},
			],
			'post_type' => [
				'type' => 'string',
				'required' => true,
//				'sanitize_callback' => function($value){
//					return (int)$value;
//				},
			],
			'offset' => [
				'type' => 'integer',
				'required' => false,
				'sanitize_callback' => function($value){
					return (int)$value;
				},
			],
		];
	}

	public function respond( WP_REST_Request $request ) {
		$post_id = $request->get_param('post_id');
		if(!$post_id){
			$recent_posts = wp_get_recent_posts([
				'post_type' => $request->get_param('post_type'),
				'numberposts' => 1,
				'post_status' => 'publish',
				'offset'      => $request->get_param('offset')
			], OBJECT);
			if(!empty($recent_posts)){
				$post = reset($recent_posts);
				$post_id = $post->ID;
			}
		}

		if(!$post_id){
			return rest_ensure_response(new \WP_Error('no_public_posts_found', __('No public posts available for preview.', 'post-to-google-my-business')));
		}

		$sample_location = new Location([
				'id' => '1',
				'group_id' => '1',
				'google_id' => 'locations/1234556766789',
				'store_code' => '',
				'title' => 'Sample location',
				'language_code' => 'en-GB',
				'website_uri' => 'http://www.example.com',
				'regular_hours' => '{"periods":[{"openDay":"SUNDAY","openTime":{"hours":9,"minutes":30},"closeDay":"SUNDAY","closeTime":{"hours":19}},{"openDay":"MONDAY","openTime":{"hours":9,"minutes":30},"closeDay":"MONDAY","closeTime":{"hours":19}},{"openDay":"TUESDAY","openTime":{"hours":9,"minutes":30},"closeDay":"TUESDAY","closeTime":{"hours":19}},{"openDay":"WEDNESDAY","openTime":{"hours":9,"minutes":30},"closeDay":"WEDNESDAY","closeTime":{"hours":19}},{"openDay":"THURSDAY","openTime":{"hours":9,"minutes":30},"closeDay":"THURSDAY","closeTime":{"hours":19}},{"openDay":"FRIDAY","openTime":{"hours":9,"minutes":30},"closeDay":"FRIDAY","closeTime":{"hours":19}},{"openDay":"SATURDAY","openTime":{"hours":9,"minutes":30},"closeDay":"SATURDAY","closeTime":{"hours":19}}]}',
				'special_hours' => '{"specialHourPeriods":[{"startDate":{"year":2022,"month":3,"day":1},"openTime":{"hours":9,"minutes":30},"endDate":{"year":2022,"month":3,"day":1},"closeTime":{"hours":19}},{"startDate":{"year":2022,"month":12,"day":31},"openTime":{"hours":9,"minutes":30},"endDate":{"year":2022,"month":12,"day":31},"closeTime":{"hours":19}}]}',
				'labels' => '',
				'metadata' => '{"canDelete":true,"placeId":"ChIJO7yvcjv3xUcRcuFu2567XWA","mapsUri":"https:\\/\\/maps.google.com\\/maps?cid=6943912491435876722","newReviewUri":"https:\\/\\/search.google.com\\/local\\/writereview?placeid=ChIJO7yvcjv3xUcRcuFu2567XWA","hasVoiceOfMerchant":true}',
				'storefront_address' => '{"regionCode":"NL","languageCode":"nl","postalCode":"1234 AB","locality":"Egmond aan de Hoef","addressLines":["Weg naar de Bleek 10a"]}',
				'service_area' => '{"businessType":"CUSTOMER_AND_BUSINESS_LOCATION","places":{"placeInfos":[{"placeName":"Alkmaar, Netherlands","placeId":"ChIJW4w217JXz0cRcHQejVreAAQ"}]},"regionCode":"NL"}',
				'imported_at' => '2026-03-03 09:55:45',
				'in_latest_import' => '1',
		]);


		$descriptions = $this->mustache_context_factory->describe_for_post($post_id, $sample_location);

		return rest_ensure_response([
			'for_post' => [
				'id'        => $post_id,
				'thumbnail' => get_the_post_thumbnail_url($post_id),
				'title'     => get_the_title($post_id),
				'excerpt'   => MbString::strimwidth(get_the_excerpt($post_id),0,40, '...'),
			],
			'descriptions' => $descriptions
		]);
	}

	public function validate( WP_REST_Request $request ): bool {
		return current_user_can('edit_posts', $request->get_param('post_id'));
	}

	public function get_methods(): array {
		return [\WP_REST_Server::READABLE];
	}

	public function get_path(): string {
		return '/variables/';
	}
}