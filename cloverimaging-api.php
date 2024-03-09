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

