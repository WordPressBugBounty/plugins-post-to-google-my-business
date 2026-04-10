<?php

namespace PGMB\REST;


use Exception;
use PGMB\ApiCache\LocationCacheRepository;
use PGMB\FormFieldParser;
use PGMB\Google\NormalizeLocationName;
use PGMB\REST\RouteInterface;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PreviewPostRoute implements RouteInterface {

	private FormFieldParser $form_field_parser;
	private $default_location;
	private LocationCacheRepository $location_cache_repository;

	public function __construct(FormFieldParser $form_field_parser, LocationCacheRepository $location_cache_repository, $default_location){

		$this->form_field_parser = $form_field_parser;
		$this->default_location = $default_location;
		$this->location_cache_repository = $location_cache_repository;
	}

	public function get_arguments(): array {
		return [
			'post_id' => [
				'type' => 'integer',
				'required' => true,
				'sanitize_callback' => function($value){
					return (int)$value;
				},
			],
			/* Raw JS serialize()'d form data string */
			'form_fields' => [
				'type' => 'string',
				'required' => true,
			],
		];
	}

	public function respond( WP_REST_Request $request ) {
		$for_post_id = $request->get_param('post_id');
		$form_fields = $request->get_param('form_fields');

		parse_str($form_fields, $output);

		$field_name =
			$output['mbp_quick_post_settings']['autopost_template']
				?? $output['mbp_form_fields']
			    ?? null;

		try{
			$parsed_form_fields = $this->form_field_parser->sanitize_from_form($field_name);
			if(empty($this->default_location) || !is_array($this->default_location)){
				throw new Exception(__('Default location is not yet configured', 'post-to-google-google-my-business'));
			}
			$location_name = reset($this->default_location);
			$location = $this->location_cache_repository->get_location_by_google_id(NormalizeLocationName::from_with_account($location_name)->without_account_id());

			if($parsed_form_fields->get_topic_type() === 'PRODUCT'){
				throw new Exception(__('Previewing products is currently not supported', 'post-to-google-google-my-business'));
			}

			$response = $parsed_form_fields->getLocalPost($location, $for_post_id);
		}catch(\Exception $e){
			$response = new \WP_Error('preview_error', sprintf(__('Could not generate preview: %s', 'post-to-google-my-business'), $e->getMessage()));
		}


		return rest_ensure_response($response);
	}

	public function validate( WP_REST_Request $request ): bool {
		return current_user_can('edit_posts', $request->get_param('post_id'));
	}

	public function get_methods(): array {
		return [\WP_REST_Server::READABLE];
	}

	public function get_path(): string {
		return '/preview/';
	}
}