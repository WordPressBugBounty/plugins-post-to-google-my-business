<?php

namespace PGMB\Components;

use PGMB\API\CachedGoogleMyBusiness;
use PGMB\API\GoogleMyBusiness;
use PGMB\Google\NormalizeLocationName;
use PGMB\Plugin;
use PGMB\PostTypes\GooglePostEntity;
use PGMB\PostTypes\GooglePostEntityRepository;
class GooglePostEntityListTable extends PrefixedListTable {
    private $parent_id;

    /**
     * @var GooglePostEntityRepository
     */
    private $repository;

    /**
     * @var GoogleMyBusiness
     */
    private $api;

    protected $html_prefix = 'pgmb-entity';

    function __construct( $parent_id, GooglePostEntityRepository $repository, CachedGoogleMyBusiness $api ) {
        $this->parent_id = (int) $parent_id;
        $this->repository = $repository;
        parent::__construct( [
            'singular' => __( 'Published GMB Post', 'post-to-google-my-business' ),
            'plural'   => __( 'Published GMB Posts', 'post-to-google-my-business' ),
            'ajax'     => true,
            'screen'   => 'post-to-gmb-entities',
        ] );
        $this->api = $api;
    }

    private $hidden_columns = [];

    function get_columns() {
        return [
            'cb'             => '<input type="checkbox" />',
            'pgmb_storecode' => __( 'Shop code', 'post-to-google-my-business' ),
            'pgmb_location'  => __( 'Location', 'post-to-google-my-business' ),
            'pgmb_status'    => __( 'Status', 'post-to-google-my-business' ),
        ];
    }

    function get_sortable_columns() {
        return [];
    }

    function get_bulk_actions() {
        return [
            'refresh_status' => __( 'Refresh status', 'post-to-google-my-business' ),
            'delete'         => __( 'Delete', 'post-to-google-my-business' ),
        ];
    }

    function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="pgmb_entity" value="%s" />', $item['entity']->get_id() );
    }

    function prepare_items() {
        $per_page = 10;
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $this->hidden_columns, $sortable];
        $current_page = $this->get_pagenum();
        $entities = $this->repository->find_by_parent( $this->parent_id )->limit( $per_page )->offset( ($current_page - 1) * $per_page );
        //		switch($_REQUEST['orderby']){
        //			case 'pgmb_location':
        //				break;
        //			case 'pgmb_status':
        //				break;
        //		}
        //
        //		if($_REQUEST['order'] === 'asc'){
        //			$entities->asc();
        //		}else{
        //			$entities->desc();
        //		}
        //$this->items = $entities->find();
        foreach ( $entities->find() as $entity ) {
            try {
                $this->api->set_user_id( $entity->get_user_key() );
                $location = $this->api->get_location( NormalizeLocationName::from_with_account( $entity->get_location_id() )->without_account_id(), 'title,storeCode' );
                $location_title = $location->title;
                $location_storecode = ( !empty( $location->storeCode ) ? $location->storeCode : '' );
            } catch ( \Exception $e ) {
                $location_title = sprintf( __( 'Could not get location name: %s', 'post-to-google-my-business' ), $e->getMessage() );
                $location_storecode = '';
            }
            $this->items[] = [
                'entity'             => $entity,
                'location_name'      => $location_title,
                'location_storecode' => $location_storecode,
            ];
        }
        $total_items = count( $entities );
        /**
         * Call to _set_pagination_args method for informations about
         * total items, items for page, total pages and ordering
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page ),
            'orderby'     => ( !empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'pgmb_location' ),
            'order'       => ( !empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'desc' ),
        ) );
    }

    public function column_pgmb_storecode( $item ) {
        return $item['location_storecode'];
    }

    public function column_pgmb_location( $item ) {
        return $item['location_name'];
    }

    public function column_pgmb_status( $item ) {
        $state = ( (( $item['entity']->get_post_state() ?: $item['entity']->get_post_error() )) ?: __( 'Unknown', 'post-to-google-my-business' ) );
        $link = ( $item['entity']->get_searchUrl() ? sprintf( "- <a href='%s' target='_blank'>%s <span class=\"dashicons dashicons-external\"></span></a>", $item['entity']->get_searchUrl(), __( 'View on Google', 'post-to-google-my-business' ) ) : '' );
        return $state . " " . $link;
    }

    function display() {
        //		/**
        //		 * Adds a nonce field
        //		 */
        //		wp_nonce_field( 'pgmb_entity_table_fetch', 'pgmb_ajax_listtable_nonce' );
        //
        //		/**
        //		 * Adds field order and orderby
        //		 */
        //		echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
        //		echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
        $singular = $this->_args['singular'];
        $this->display_tablenav( 'top-pgmb-entities' );
        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
		<table class="wp-list-table pgmb-post-entities <?php 
        echo implode( ' ', $this->get_table_classes() );
        ?>">
			<thead>
			<tr>
				<?php 
        $this->print_column_headers();
        ?>
			</tr>
			</thead>

			<tbody id="pgmb-entity-list"
				<?php 
        if ( $singular ) {
            echo " data-wp-lists='list:{$singular}'";
        }
        ?>
			>
			<?php 
        $this->display_rows_or_placeholder();
        ?>
			</tbody>

			<tfoot>
			<tr>
				<?php 
        $this->print_column_headers( false );
        ?>
			</tr>
			</tfoot>

		</table>
		<?php 
        $this->display_tablenav( 'bottom-pgmb-entities' );
    }

}
