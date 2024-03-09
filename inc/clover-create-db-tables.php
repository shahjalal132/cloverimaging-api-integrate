<?php

/**
 * Create DB Tables
 * 
 * Get-all-product-types
 * products
 * availability
 * prices
 * invoices
 */


// Create sync_clover_product_types Table When Plugin Activated
function clover_product_types_db_table_create() {

    global $wpdb;

    $table_name      = $wpdb->prefix . 'sync_clover_product_types';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Create wp_sync_clover_products Table When Plugin Activated
function clover_products_db_table_create() {

    global $wpdb;

    $table_name      = $wpdb->prefix . 'sync_clover_products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        status VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Create wp_sync_clover_availability Table When Plugin Activated
function clover_availability_db_table_create() {

    global $wpdb;

    $table_name      = $wpdb->prefix . 'wp_sync_clover_availability';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Create wp_sync_clover_prices Table When Plugin Activated
function clover_prices_db_table_create() {

    global $wpdb;

    $table_name      = $wpdb->prefix . 'wp_sync_clover_prices';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Create wp_sync_clover_invoices Table When Plugin Activated
function clover_invoices_db_table_create() {

    global $wpdb;

    $table_name      = $wpdb->prefix . 'wp_sync_clover_invoices';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}