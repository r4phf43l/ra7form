<?php

/* 
 * Plugin Name: ra7form
 * Author: rafaantonio
 * Text Domain: ra7form
 * Domain Path: /languages
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ra7form_List extends WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Recipient', 'ra7form' ),
			'plural'   => __( 'Recipients', 'ra7form' ),
			'ajax'     => false
		] );
	}

	public static function get_recipients( $per_page = 5, $page_number = 1 ) {
		global $wpdb;
		$sql = "SELECT id, post_title, post_content FROM {$wpdb->prefix}posts WHERE post_type = 'wpcf7_contact_form'";		
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}	

	public static function record_count() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'wpcf7_contact_form'";
		return $wpdb->get_var( $sql );
	}
	
	public function no_items() {
		_e( 'No forms avaliable.', 'ra7form' );
	}

    function column_recipient ( $item ) {
        $key = '_mail';
        $it = get_post_meta( $item['id'], $key );
        return htmlentities( $it[0]['recipient'] );
    }

    function column_sender ( $item ) {
        $key = '_mail';
        $it = get_metadata_raw( 'post', $item['id'], $key );
        return htmlentities( $it[0]['sender'] );
    }

	function column_post_title( $item ) {
		$edit_link = add_query_arg(
			array(
				'post'	 => absint( $item['id'] ),
				'action' => 'edit',
                'page'	 => esc_attr( $_REQUEST['page'] ),
                '_nonce' => wp_create_nonce( 'ra7-edit-recipient-' . $item['id'] )
			),
			menu_page_url( 'ra7form', false )
		);
		$output = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $edit_link ),
			esc_html( $item['post_title'] )
		);
		$output = sprintf( '<strong>%s</strong>', $output );
		return $output;
	}
	
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $column_name !== $primary ) {
			return '';
        }

        $edit_link = add_query_arg(
			array(
				'post'   => absint( $item['id'] ),
				'action' => 'edit',
                'page'   => esc_attr( $_REQUEST['page'] ),
                '_nonce' => wp_create_nonce( 'ra7-edit-recipient-' . $item['id'] )
			),
			menu_page_url( 'ra7form', false )
        );

        $report_link = add_query_arg(
			array(
				'post'   => absint( $item['id'] ),
				'action' => 'report',
                'page'   => esc_attr( $_REQUEST['page'] ),
                '_nonce' => wp_create_nonce( 'ra7-edit-recipient-' . $item['id'] )
			),
			menu_page_url( 'ra7form', false )
        );        
        
        $output_edit = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $edit_link ),
			esc_html( __( 'Edit', 'ra7form' ) )
        );
        
        $output_report = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $report_link ),
			esc_html( __( 'Report', 'ra7form' ) )
		);

        $actions = array (
            'edit' => $output_edit,
            'report' => $output_report,
        );

		return $this->row_actions( $actions );
	}

	function get_columns() {
		$columns = [			
			'post_title' => __( 'Form', 'ra7form' ),
            'recipient'  => __( 'Recipient', 'ra7form' ),
            'sender'	 => __( 'Sender', 'ra7form' ),
		];
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', true ),
		);
		return $sortable_columns;
	}

	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();		
		$per_page     = $this->get_items_per_page( 'recipients_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
		$this->items = self::get_recipients( $per_page, $current_page );
    }    
}
