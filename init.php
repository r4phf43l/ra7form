<?php
/*
 * Plugin Name: ra7form
 * Plugin URI: https://rafaantonio.com.br/ra7form/
 * Description: Permite gerenciar multiplos destinatários no Contact 7 Form.
 * Requirements: Contact 7 Form
 * Depends: Contact 7 Form
 * Version: 0.1.0
 * Author: RafaAntonio (r4phf43l)
 * Author URI: https://rafaantonio.com.br/
 * License: MIT
 * License URI: https://opensource.org/licenses/mit-license.php
 * Text Domain: ra7form
 * Domain Path: /languages
*/

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'Ra7form_Plugin' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/include/class-ra7form-plugin.php' );
}

if ( ! class_exists( 'Ra7form_List' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/include/class-ra7form-list.php' );
}

if ( ! class_exists( 'Ra7form_Recipient' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/include/class-ra7form-recipient.php' );
}

add_action( 'plugins_loaded', function () {
	Ra7form_Plugin::get_instance();
} );

add_action( 'init', function () {
	load_plugin_textdomain( 'ra7form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );
