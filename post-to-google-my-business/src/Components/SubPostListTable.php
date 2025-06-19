<?php

/*
 * Based on WP List table ajax by debba
 * https://github.com/debba/wp-list-table-ajax-sample
 */

namespace PGMB\Components;

use DateTime;
use DateTimeZone;
use PGMB\PostTypes\SubPost;
use PGMB\PostTypes\SubPostRepository;
use PGMB\Util\DateTimeCompat;
use PGMB\Util\MbString;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTime;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone;


class SubPostListTable extends PrefixedListTable {

	private $parent_id;
	/**
	 * @var SubPostRepository
	 */
	private $repository;

    protected $html_prefix = 'pgmb-subpost';

	/**
	 *
	 * @Override of constructor
	 * Constructor take 3 parameters:
	 * singular : name of an element in the List Table
	 * plural : name of all of the elements in the List Table
	 * ajax : if List Table supports AJAX set to true
	 *
	 */

	function __construct($parent_id, SubPostRepository $repository) {
		$this->parent_id = $parent_id;
		$this->repository = $repository;

		parent::__construct(
			array(
				'singular'  => __('GMB Post', 'post-to-google-my-business'),
				'plural'    => __('GMB Posts', 'post-to-google-my-business'),
				'ajax'      => true,
                'screen'    => 'post-to-gmb-subpost',
			)
		);


	}

	/**
	 * @return array
	 *
	 * The array is associative :
	 * keys are slug columns
	 * values are description columns
	 *
	 */
	function get_columns() {
		return array(
			'cb'        => '<input type="checkbox" />',
//			'pgmb_id'      => 'ID',
			'pgmb_post_type'   => __('Post type', 'post-to-google-my-business'),
			'pgmb_publish_date'  => __('Publish date', 'post-to-google-my-business'),
			'pgmb_date_created'    => __('Created', 'post-to-google-my-business')
		);

	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 *
	 * Method column_default let at your choice the rendering of everyone of column
	 *
	 */

	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'pgmb_id':
			case 'pgmb_post_type':
			case 'pgmb_publish_date':
			case 'pgmb_date_created':
//				return $item[ $column_name ];
			default:
				return print_r( $item, true );
		}
	}

	/**
	 * @var array
	 *
	 * Array contains slug columns that you want hidden
	 *
	 */

	private $hidden_columns = array(
		'pgmb_id'
	);

	/**
	 * @return array
	 *
	 * The array is associative :
	 * keys are slug columns
	 * values are array of slug and a boolean that indicates if is sorted yet
	 *
	 */

	function get_sortable_columns() {
		return $sortable_columns = array(
//			'pgmb_post_type'	 	=> array( 'pgmb_post_type', false ), // not possible due to this being saved in an array
			'pgmb_publish_date'	=> array( 'pgmb_publish_date', false ),
			'pgmb_date_created'  => array( 'pgmb_date_created', false )
		);
	}

	function get_bulk_actions() {
		return [
            'delete'    => __('Delete', 'post-to-google-my-business'),
        ];
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="pgmb_subpost" value="%s" />', $item->get_id()
        );
	}

	/**
	 * @Override of prepare_items method
	 *
	 */

	function prepare_items() {

		/**
		 * How many records for page do you want to show?
		 */
		$per_page = 5;

		/**
		 * Define of column_headers. It's an array that contains:
		 * columns of List Table
		 * hiddens columns of table
		 * sortable columns of table
		 * optionally primary column of table
		 */
		$columns  = $this->get_columns();
		$hidden   = $this->hidden_columns;
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/**
		 * Get current page calling get_pagenum method
		 */
		$current_page = $this->get_pagenum();

		$posts = $this->repository
            ->find_by_parent($this->parent_id)
            ->limit($per_page)
            ->offset((($current_page-1)*$per_page));

        if(isset($_REQUEST['orderby'])){
	        switch($_REQUEST['orderby']){
		        case 'pgmb_publish_date':
			        $posts->order_by_publish_date();
			        break;
		        case 'pgmb_date_created':
			        $posts->order_by_creation_date();
	        }
        }

        if(isset($_REQUEST['order']) && $_REQUEST['order'] === 'asc'){
            $posts->asc();
        }else{
            $posts->desc();
        }


		$this->items = $posts->find();

		$total_items = count($posts);

		/**
		 * Call to _set_pagination_args method for informations about
		 * total items, items for page, total pages and ordering
		 */
		$this->set_pagination_args(
			array(
				'total_items'	=> $total_items,
				'per_page'	    => $per_page,
				'total_pages'	=> ceil( $total_items / $per_page ),
				'orderby'	    => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'pgmb_date_created',
				'order'		    => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'desc'
			)
		);
	}


	public function single_row( $item ) {
        $is_processing = (bool)$item->get_queued_items();
		echo '<tr data-is_processing="'.$is_processing.'" data-postid="'.$item->get_id().'"  class="mbp-post'.($item->has_error() ? ' mbp-has-error' : '').'">'; //'.($has_error ? ' mbp-has-error' : '').'
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * The Google My Business post types
	 *
	 * @return array
	 */
	public function gmb_topic_types(){
		return array(
			'STANDARD'	=> array(
				'name'		=> __('What\'s New', 'post-to-google-my-business'),
				'dashicon'	=> 'dashicons-megaphone'
			),
			'EVENT'		=> array(
				'name'		=> __('Event', 'post-to-google-my-business'),
				'dashicon'	=> 'dashicons-calendar'
			),
			'OFFER'		=> array(
				'name'		=> __('Offer', 'post-to-google-my-business'),
				'dashicon'	=> 'dashicons-tag'
			),
			'PRODUCT'	=> array(
				'name'		=> __('Product', 'post-to-google-my-business'),
				'dashicon'	=> 'dashicons-cart'
			),
			'ALERT'     => [
				'name'      => __('COVID-19 update', 'post-to-google-my-business'),
				'dashicon'  => 'dashicons-sos'
			]
		);
	}

	public function column_pgmb_post_type(SubPost $item){
		$actions = [
			'postlist'      => sprintf('<a href="#" data-action="postlist" class="mbp-action">%s</a>', __('List created posts', 'post-to-google-my-business')),
			'edit'          => sprintf('<a href="#" data-action="edit" class="mbp-action">%s</a>', __('Edit', 'post-to-google-my-business')),
			'duplicate'     => sprintf('<a href="#" data-action="duplicate" class="mbp-action">%s</a>', __('Duplicate', 'post-to-google-my-business')),
			'trash'         => sprintf('<a href="#" data-action="trash" class="submitdelete mbp-action">%s</a>', __('Delete', 'post-to-google-my-business')),
		];

		if($item->is_draft()){
			unset($actions['postlist']);
		}

	    $topic_type = $item->parsed_form_fields()->get_topic_type();

	    $output = '';

	    if($item->parsed_form_fields()->is_repost()){
	        $output .= sprintf('<span class="dashicons dashicons-controls-repeat" title="%s"></span> ', __('Repost enabled', 'post-to-google-my-business'));
        }

        $output .= sprintf('<a href="#" class="row-title mbp-action" data-action="edit"><span class="dashicons %s"></span> %s</a><br />%s',
            $this->gmb_topic_types()[$topic_type]['dashicon'],
	        $this->gmb_topic_types()[$topic_type]['name'],
            MbString::strimwidth((string)$item->parsed_form_fields()->get_summary(), 0, 100, '...')
        );

        return $output.$this->row_actions($actions);
    }

    public function column_pgmb_publish_date(SubPost $item){
	    if($item->is_draft()){
		    return '-';
	    }

        $publish_date_timestamp = $item->get_post_publish_date_timestamp();
	    $publish_datetime = (new DateTime())->setTimestamp($publish_date_timestamp)->setTimezone(DateTimeCompat::get_timezone());

	    $publish_output = '<span class="dashicons dashicons-clock"></span>';
	    $now = new DateTime('now', DateTimeCompat::get_timezone());
	    if($publish_datetime < $now){
		    $publish_output = '<span class="dashicons dashicons-admin-site"></span>';
	    }
	    $publish_output .= DateTimeCompat::format_date($publish_datetime).' '.DateTimeCompat::format_time($publish_datetime);

        $queued_items = (int)$item->get_queued_items();
        if($queued_items > 0){
	        $publish_output .= '<br /><span style="float:left" class="spinner is-active"></span><span class="pgmb-items-processing">'.sprintf(esc_html__('%d publishing tasks queued', 'post-to-google-my-business'), $queued_items).'</span>';
        }

        $delete_event = wp_get_scheduled_event('pgmb_delete_previous_post', [$item->get_id()]);
        if($delete_event){
	        $publish_output .= '<br /><span title="'.esc_attr__('This post will be deleted when the next post is auto-posted', 'post-to-google-my-business').'"><span class="dashicons dashicons-clock"></span><span class="dashicons dashicons-trash"></span>';
	        $delete_datetime = (new DateTime())->setTimestamp($delete_event->timestamp)->setTimezone(DateTimeCompat::get_timezone());
	        $publish_output .= DateTimeCompat::format_date($delete_datetime).' '.DateTimeCompat::format_time($delete_datetime);
            $publish_output .= '</span>';
        }

	    return $publish_output;
    }

    public function column_pgmb_date_created(SubPost $item){
        if($item->is_draft()){
            return __('Draft', 'post-to-google-my-business');
        }
        return sprintf( _x( '%s ago', '%s = human-readable time difference', 'post-to-google-my-business' ), human_time_diff($item->get_creation_timestamp()));
    }

	/**
	 * @Override of display method
	 */

	function display() {

//		/**
//		 * Adds a nonce field
//		 */
//		wp_nonce_field( 'pgmb_subpost_table_fetch', 'pgmb_subpost_table_nonce' );
//
//		/**
//		 * Adds field order and orderby
//		 */
//		echo '<input type="hidden" id="pgmb_order" name="pgmb_order" value="' . $this->_pagination_args['order'] . '" />';
//		echo '<input type="hidden" id="pgmb_orderby" name="pgmb_orderby" value="' . $this->_pagination_args['orderby'] . '" />';

		$singular = $this->_args['singular'];

		//$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
		<table class="wp-list-table mbp-existing-posts <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tbody id="pgmb-subpost-list"
				<?php
				if ( $singular ) {
					echo " data-wp-lists='list:$singular'";
				}
				?>
			>
			<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>

		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}


}
