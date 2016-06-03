<?php
/*
Plugin Name: WP eCommerce Virtual Merchant Gateway
Plugin URI: https://wpecommerce.org
Version: 1.0
Author: WP eCommerce
Description: A plugin that allows the store owner to process payments using Virtual Merchant
Author URI:  https://wpecommerce.org
*/

define( 'WPECVM_VERSION', '3.1' );
define( 'WPECVM_PRODUCT_ID', '' );

if ( ! defined( 'WPECVM_PLUGIN_DIR' ) ) {
	define( 'WPECVM_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'WPECVM_PLUGIN_URL' ) ) {
	define( 'WPECVM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

include_once( WPECVM_PLUGIN_DIR . '/includes/functions.php' );

function wpec_vmerchant_init() {
	include_once( WPECVM_PLUGIN_DIR . '/class-virtualmerchant.php');
}
add_action( 'wpsc_init', 'wpec_vmerchant_init' );

// register the gateway
function wpec_add_vmerchant_gateway( $nzshpcrt_gateways ) {
	$num = count( $nzshpcrt_gateways ) + 1;
	
	foreach ( $nzshpcrt_gateways as $gateway ) {
		if( $gateway['internalname'] == 'vmerchant' ) {
			unset( $gateway );
		}
	}

	$nzshpcrt_gateways[$num] = array(
		'name' => 'Virtual Merchant',
		'api_version' => 2.0,
		'class_name' => 'wpec_merchant_virtualmerchant',
		'has_recurring_billing' => false,
		'display_name' => 'Credit Card',	
		'wp_admin_cannot_cancel' => false,
		'requirements' => array(
			'php_version' => 5.0
		),
		'form' => 'wpec_virtualmerchant_settings_form',
		'submit_function' => 'wpec_save_virtualmerchant_settings',
		'internalname' => 'wpec_virtualmerchant',
		'display_name' => "Credit Card"
	);
	return $nzshpcrt_gateways; 
}
add_filter( 'wpsc_merchants_modules', 'wpec_add_vmerchant_gateway', 100 );
?>