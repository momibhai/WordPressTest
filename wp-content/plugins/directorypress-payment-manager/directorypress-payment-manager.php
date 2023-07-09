<?php

/**
 * Plugin Name:       DirectoryPress Payment Manager
 * Plugin URI:        https://designinvento.net/downloads/directorypress-payment-manager/
 * Description:       This plugin provides paid listing functionality with woocomerce payment system to extend Directorypress plugin.
 * Version:           3.0.0
 * Author:            Designinvento
 * Author URI:        https://designinvento.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       directorypress-payment-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DIRECTORYPRESS_PAYMENT_MANAGER_VERSION', '3.0.0' );
define('DPPM_PATH', plugin_dir_path(__FILE__));
define('DPPM_URL', plugins_url('/', __FILE__));
define( 'DPPM_TEMPLATES_PATH', DPPM_PATH . 'public/');

if(is_admin() && in_array('directorypress/directorypress.php', apply_filters('active_plugins', get_option('active_plugins')))){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$directorypress_data = get_plugin_data( WP_PLUGIN_DIR .'/directorypress/directorypress.php' );
	if(version_compare($directorypress_data['Version'], '3.4.0', '<') ){
		deactivate_plugins(plugin_basename(__FILE__));
	}
}

function activate_directorypress_payment_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-payment-manager-activator.php';
	Directorypress_Payment_Manager_Activator::activate();
}

function deactivate_directorypress_payment_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-payment-manager-deactivator.php';
	Directorypress_Payment_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_directorypress_payment_manager' );
register_deactivation_hook( __FILE__, 'deactivate_directorypress_payment_manager' );

require plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-payment-manager.php';

function run_directorypress_payment_manager() {

	$directorypress_payment_manager_instance = new Directorypress_Payment_Manager();
	$directorypress_payment_manager_instance->run();

}
add_action( 'directorypress_loaded', 'run_directorypress_payment_manager' );
