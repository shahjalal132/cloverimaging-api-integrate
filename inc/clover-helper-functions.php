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

/**
 * Function to save response data to a file.
 *
 * @param string $data The response data to be saved.
 */
function put_invoices_response_data( $data ) {
    // Ensure directory exists to store response data
    $directory = CLOVER_PLUGIN_PATH . '/data/';

    if ( !file_exists( $directory ) ) {
        mkdir( $directory, 0777, true );
    }

    // Construct file path for response data
    $fileName = $directory . 'invoices.json';

    // Write response data to file
    if ( file_put_contents( $fileName, $data ) !== false ) {
        return "Data written to file successfully.";
    } else {
        return "Failed to write data to file.";
    }
}



// display additional information
// Function to display additional information in the single product page under the Additional Information tab
function display_custom_information_in_additional_tab( $product_attributes ) {
    global $product;

    // get product id
    $product_id = $product->get_id();

    // Get custom product information from meta keys
    $color               = get_post_meta( $product_id, '_jcolor', true );
    $type                = get_post_meta( $product_id, '_type', true );
    $productID           = get_post_meta( $product_id, '_productID', true );
    $productNO           = get_post_meta( $product_id, '_productNO', true );
    $Manufacturer        = get_post_meta( $product_id, '_Manufacturer', true );
    $videos              = get_post_meta( $product_id, '_Videos', true );
    $Yield               = get_post_meta( $product_id, '_Yield', true );
    $Compatible_Printers = get_post_meta( $product_id, '_Compatible_Printers', true );

    // Add custom information to the product attributes array
    $product_attributes['clover-color'] = array(
        'label' => __( 'Color' ),
        'value' => esc_html( $color ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-type'] = array(
        'label' => __( 'Type' ),
        'value' => esc_html( $type ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-productID'] = array(
        'label' => __( 'Product ID' ),
        'value' => esc_html( $productID ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-productNO'] = array(
        'label' => __( 'Product No' ),
        'value' => esc_html( $productNO ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-Manufacturer'] = array(
        'label' => __( 'Manufacturer' ),
        'value' => esc_html( $Manufacturer ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-videos'] = array(
        'label' => __( 'Videos' ),
        'value' => esc_html( $videos ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-Yield'] = array(
        'label' => __( 'Yield' ),
        'value' => esc_html( $Yield ),
    );

    // Add custom information to the product attributes array
    $product_attributes['clover-Compatible-Printers'] = array(
        'label' => __( 'Compatible Printers' ),
        'value' => esc_html( $Compatible_Printers ),
    );

    // Return the modified product attributes array
    return $product_attributes;
}

// Hook the function to the 'woocommerce_display_product_attributes' filter
add_filter( 'woocommerce_display_product_attributes', 'display_custom_information_in_additional_tab' );