<?php

namespace PGMB\Mustache\Context;


use PGMB\ApiCache\Location;
use PGMB\Mustache\MustacheContextProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LocationContext implements MustacheContextProviderInterface {

	/**
	 * @var Location
	 */
	private $location;

	public function __construct(Location $location) {
		$this->location = $location;
	}

	/**
	 * @inheritDoc
	 */
	public function get_key(): string {
		return 'location';
	}

	/**
	 * @inheritDoc
	 */
	public function build(): array {
		return (array)$this->location->api_formatted();

	}

	public function describe(): array {
		return [
			'label' => __('Google location details', 'post-to-google-my-business'),
			'children' => [
				'title' => [
					'type'  => 'string',
					'label' => __('Location name/title', 'post-to-google-my-business'),
					'example' => 'My Business Name',
				],
				'storeCode' => [
					'type'  => 'string',
					'label' => __('Location store code', 'post-to-google-my-business'),
					'example' => '123',
				],
				'websiteUri' => [
					'type'  => 'url',
					'label' => __('Location website URL', 'post-to-google-my-business'),
					'example' => '123',
				],
				'labels' => [
					'type' => 'list',
					'label' => 'List of labels associated with the location',
				],
				'storefrontAddress' => [
					'type' => 'object',
					'label' => 'Location address',
					'children' => [
						'postalCode' => [
							'type' => 'string',
							'label' => 'Postal code',
						],
						'regionCode' => [
							'type' => 'string',
							'label' => 'Region code',
							'example' => 'US',
						],
						'locality' => [
							'type' => 'string',
							'label' => 'City or town',
							'example' => 'New York',
						],
						'addressLines' => [
							'type' => 'list',
							'label' => 'Lines of the address'
						]
					]
				],
				'metadata' => [
					'type' => 'object',
					'label' => 'Location meta data',
					'children' => [
						'placeId' => [
							'type' => 'string',
							'label' => 'Place ID'
						],
						'mapsUri' => [
							'type' => 'url',
							'label' => 'Link to location on Google Maps'
						],
						'newReviewUri' => [
							'type' => 'url',
							'label' => 'Link to leave a review for location'
						],
					]
				]
			]
		];
	}
}