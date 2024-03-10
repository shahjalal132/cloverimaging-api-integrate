<?php

/*
 * Plugin Name:       cloverimaging api integrate
 * Plugin URI:        #
 * Description:       cloverimaging api integrate
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            imjol
 * Author URI:        https://imjol.com/
 */

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

// Define plugin path
if ( !defined( 'CLOVER_PLUGIN_PATH' ) ) {
    define( 'CLOVER_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Define plugin url
if ( !defined( 'CLOVER_PLUGIN_URI' ) ) {
    define( 'CLOVER_PLUGIN_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

// require autoload file
require_once CLOVER_PLUGIN_PATH . '/vendor/autoload.php';


//Create sync_clover_product_types table when the plugin activate
register_activation_hook( __FILE__, 'clover_product_types_db_table_create' );

//Create sync_clover_products table when the plugin activate
register_activation_hook( __FILE__, 'clover_products_db_table_create' );

//Create wp_sync_clover_availability table when the plugin activate
register_activation_hook( __FILE__, 'clover_availability_db_table_create' );

//Create wp_sync_clover_prices table when the plugin activate
register_activation_hook( __FILE__, 'clover_prices_db_table_create' );

//Create wp_sync_clover_invoices table when the plugin activate
register_activation_hook( __FILE__, 'clover_invoices_db_table_create' );