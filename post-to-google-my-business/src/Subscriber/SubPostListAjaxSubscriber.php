<?php

namespace PGMB\Subscriber;

use PGMB\Components\SubPostListTable;
use PGMB\EventManagement\SubscriberInterface;
use PGMB\PostTypes\SubPostRepository;

class SubPostListAjaxSubscriber implements SubscriberInterface {

	/**
	 * @var SubPostRepository
	 */
	private $repository;

	public static function get_subscribed_hooks() {
		return [
			'wp_ajax_pgmb_subpost_list_display'     => 'display_list',
			'wp_ajax_pgmb_subpost_list_update'      => 'update_list',
			'wp_ajax_pgmb_subpost_bulk_action'      => 'process_bulk_action'
		];
	}

	public function __construct(SubPostRepository $repository){
		$this->repository = $repository;
	}

	public function process_bulk_action(){
		check_ajax_referer( 'pgmb_subpost_table_fetch', 'ajax_list_table_nonce', true );

		$bulk_action = sanitize_text_field($_REQUEST['bulk_action']);

		$ids = $_REQUEST['post_ids'];
		if(!is_array($ids) || empty($ids)){
			return;
		}

		if($bulk_action === 'delete'){
			foreach($ids as $id){
				$subpost = $this->repository->find_by_id((int)$id);
				if($subpost){
					$this->repository->delete($subpost);
				}
			}
		}
		wp_send_json_success();
	}

	public function display_list() {

		check_ajax_referer( 'pgmb_subpost_table_fetch', 'ajax_list_table_nonce', true );

		$parent_post_id = (int)$_REQUEST['parent_id'];

		$wp_list_table = new SubPostListTable($parent_post_id, $this->repository);
		$wp_list_table->prepare_items();

		ob_start();
		$wp_list_table->display();
		$display = ob_get_clean();

		die(

		json_encode(array(

			"display" => $display

		))

		);
	}

	public function update_list() {

		$parent_post_id = (int)$_REQUEST['parent_id'];

		$wp_list_table = new SubPostListTable($parent_post_id, $this->repository);
		$wp_list_table->ajax_response();

	}
}
