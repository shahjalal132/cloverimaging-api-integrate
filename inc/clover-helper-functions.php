<?php

/**
 * Function to save response data to a file.
 *
 * @param string $data The response data to be saved.
 */
function put_product_types_response_data( $data ) {
    // Ensure directory exists to store response data
    $directory = CLOVER_PLUGIN_PATH . '/data/';
    
    if ( !file_exists( $directory ) ) {
        mkdir( $directory, 0777, true );
    }

    // Construct file path for response data
    $fileName = $directory . 'product_types.json';

    // Write response data to file
    if ( file_put_contents( $fileName, $data ) !== false ) {
        return "Data written to file successfully.";
    } else {
        return "Failed to write data to file.";
    }
}

/**
 * Function to save response data to a file.
 *
 * @param string $data The response data to be saved.
 */
function put_products_response_data( $data ) {
    // Ensure directory exists to store response data
    $directory = CLOVER_PLUGIN_PATH . '/data/';
    
    if ( !file_exists( $directory ) ) {
        mkdir( $directory, 0777, true );
    }

    // Construct file path for response data
    $fileName = $directory . 'products.json';

    // Write response data to file
    if ( file_put_contents( $fileName, $data ) !== false ) {
        return "Data written to file successfully.";
    } else {
        return "Failed to write data to file.";
    }
}