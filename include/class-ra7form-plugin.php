<?php

/* 
 * Plugin Name: ra7form
 * Author: rafaantonio
 * Text Domain: ra7form
 * Domain Path: /languages
 */

class Ra7form_Plugin {
	static $instance;
	public $inpage;
	public $recipients_obj;

	public function __construct() {
		$act = filter_input( INPUT_GET, 'action' );
		$this->inpage = ( $act ) ? 0 : 1;
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}

	public function plugin_menu() {
		if ( $this->inpage ) {
			$inner = [ $this, 'plugin_settings_page' ];
		} else {
			$inner = [ $this, 'plugin_editor_page' ];
		}		
		$hook = add_menu_page(
			__( 'Recipients for Contact Form 7', 'ra7form' ),
			__( 'Recipients', 'ra7form' ),
			'manage_options',
			'ra7form',
			$inner,
			'dashicons-email-alt2',
			58
		);
		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}

	public function plugin_settings_page() {		
		include_once( plugin_dir_path( __FILE__ ) . '/ra7form-list-template.php' );
	}

	public function plugin_editor_page() {
		$rec = filter_input( INPUT_GET, 'post' );
		$this->recipients_obj = new ra7form_Recipient( $rec );
		$this->recipients_obj->prepare_item();
		$this->recipients_obj->display();
	}

	public function screen_option() {
		if ( $this->inpage ) {
			$option = 'per_page';
			$args   = [
				'label'   => __( 'Recipients', 'ra7form' ),
				'default' => 5,
				'option'  => 'recipients_per_page'
			];
			add_screen_option( $option, $args );		
			$this->recipients_obj = new Ra7form_List();			
		}		
	}

	public static function get_instance() {
		if ( !isset( self::$instance ) ) { self::$instance = new self(); }
		return self::$instance;
	}
}
