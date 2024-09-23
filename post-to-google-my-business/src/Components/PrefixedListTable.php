<?php

namespace PGMB\Components;

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

abstract class PrefixedListTable extends \WP_List_Table {

	protected $html_prefix = "";
	/**
	 * Replace the static HTML IDs from the parent function
	 *
	 * @param string $which
	 */
	public function bulk_actions( $which = '' ) {
		ob_start();
		parent::bulk_actions( $which );
		$bulk_actions = ob_get_clean();

		echo str_replace(
			[
				'doaction',
				'"action'
			],
			[
				"{$this->html_prefix}-do-bulk-action",
				"\"{$this->html_prefix}-bulk-action"
			],
			$bulk_actions);
	}

	public function print_column_headers( $with_id = true ) {
		ob_start();
		parent::print_column_headers( $with_id );
		$column_headers = ob_get_clean();
		echo str_replace(['cb-select-all-', "id='cb'"], ["{$this->html_prefix}-cb-select-all-", "id='{$this->html_prefix}-cb'"], $column_headers);
	}

	function ajax_response() {

		check_ajax_referer( 'pgmb_subpost_table_fetch', 'ajax_list_table_nonce'  );

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();
		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();

		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		die( json_encode( $response ) );
	}
}
