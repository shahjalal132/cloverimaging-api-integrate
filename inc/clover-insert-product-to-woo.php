<?php

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

try {
    // Function to insert products into WooCommerce
    function clover_product_insert_woocommerce() {

        // Get global $wpdb object
        global $wpdb;

        // Define table names
        $table_name = $wpdb->prefix . 'sync_clover_products';

        // Retrieve pending products from the database
        $products = $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'pending' LIMIT 1" );

        // WooCommerce store information
        $website_url     = home_url();
        $consumer_key    = 'ck_e777c6842253c7d87394c3ab3207b09d2562d44e';
        $consumer_secret = 'cs_2226f38aaf0256251b07d5177f414fe325184cf9';

        foreach ( $products as $product ) {

            // get operation_value from $product
            $product_data = $product->operation_value;

            // decode operation_value
            $product_data = json_decode( $product_data );

            /* echo '<pre>';
            print_r( $product_data );
            wp_die(); */

            // retrieve product data
            $sku          = $product_data->ID;
            $title        = $product_data->title;
            $no           = $product_data->no;
            $type         = $product_data->type;
            $color        = $product_data->color;
            $availability = $product_data->availability;
            $yield        = $product_data->yield ?? '';

            // get oemNos and manufacturer
            $oemNos        = $product_data->oemNos;
            $manufacturers = '';
            $ome_nos       = '';
            if ( !empty( $oemNos ) && is_array( $oemNos ) ) {
                foreach ( $oemNos as $oneNo ) {
                    $manufacturers .= $oneNo->manufacturer . ',';
                    $ome_nos .= $oneNo->oemNo . ',';
                }
            }

            $manufacturers = rtrim( $manufacturers, ',' );
            $ome_nos       = rtrim( $ome_nos, ',' );

            // get images
            $images = $product_data->images ?? [];
            // set image limit 5
            $images = array_slice( $images, 0, 5 );

            // get videos
            $videos     = $product_data->videos;
            $video_urls = '';
            if ( !empty( $videos ) && is_array( $videos ) ) {
                foreach ( $videos as $video ) {
                    $video_urls .= $video . ',';
                }
            }

            $video_urls = rtrim( $video_urls, ',' );

            // get attributes
            $attributes       = $product_data->attributes ?? [];
            $attribute_names  = '';
            $attribute_values = '';
            if ( !empty( $attributes ) && is_array( $attributes ) ) {
                foreach ( $attributes as $attribute ) {
                    $attribute_names .= $attribute->attributeName . '|';
                    $attribute_values .= $attribute->attributeValue . '|';
                }
            }

            $serviceLevels = $product_data->serviceLevels;
            // get price from serviceLevels
            $price = $serviceLevels[0]->price;
            // increase 30% price
            $price = round( $price * 1.3 );

            $compatiblePrinters = $product_data->compatiblePrinters;
            // Get compatible printers
            $printer_names = [];
            if ( !empty( $compatiblePrinters ) ) {
                foreach ( $compatiblePrinters as $printer ) {
                    $printer_names[] = $printer->manufacturer . ' ' . $printer->model;
                }
            }

            // get unique value from $printer_names
            $printer_names = array_unique( $printer_names );

            // get product dimensions and measurements
            $productBoxDimensions = $product_data->productBoxDimensions;
            $unitOfMeasure        = $productBoxDimensions->unitOfMeasure ?? '';
            $length               = $productBoxDimensions->length ?? '';
            $width                = $productBoxDimensions->width ?? '';
            $height               = $productBoxDimensions->height ?? '';
            $weight               = $productBoxDimensions->weight ?? '';

            $description = $product_data->additionalProductInformation;

            $searchKeywords = $product_data->searchKeywords;
            // get unique value from $searchKeywords
            $searchKeywords = array_unique( $searchKeywords );

            // Set up the API client with WooCommerce store URL and credentials
            $client = new Client(
                $website_url,
                $consumer_key,
                $consumer_secret,
                [
                    'verify_ssl' => false,
                    'wp_api'     => true,
                    'timeout'    => 60,
                    'version'    => 'wc/v3',
                ]
            );

            // Check if the product already exists in WooCommerce
            $args = [
                'post_type'  => 'product',
                'meta_query' => [
                    [
                        'key'     => '_sku',
                        'value'   => $sku,
                        'compare' => '=',
                    ],
                ],
            ];

            // Check if the product already exists
            $existing_products = new WP_Query( $args );

            if ( $existing_products->have_posts() ) {
                $existing_products->the_post();

                // get product id
                $product_id = get_the_ID();

                // Update the status of the processed product database
                $wpdb->update(
                    $table_name,
                    [ 'status' => 'completed' ],
                    [ 'id' => $product->id ]
                );

                // Update the product  if already exists
                $product_data_to_insert = [
                    'name'        => $title,
                    'sku'         => "$sku",
                    'type'        => 'simple',
                    'description' => $description,
                    'attributes'  => [],
                ];

                // update product
                $client->put( 'products/' . $product_id, $product_data_to_insert );

            } else {

                // Update the status of the processed product database
                $wpdb->update(
                    $table_name,
                    [ 'status' => 'completed' ],
                    [ 'id' => $product->id ]
                );

                // Create a new product if not exists
                $product_data_to_insert = [
                    'name'        => $title,
                    'sku'         => "$sku",
                    'type'        => 'simple',
                    'description' => $description,
                    'attributes'  => [],
                ];

                // Create the product
                $product    = $client->post( 'products', $product_data_to_insert );
                $product_id = $product->id;

                // Set product information
                wp_set_object_terms( $product_id, 'simple', 'product_type' );
                update_post_meta( $product_id, '_visibility', 'visible' );

                if ( 'In Stock' == $availability ) {
                    update_post_meta( $product_id, '_stock_status', 'instock' );
                } else {
                    update_post_meta( $product_id, '_stock_status', 'outofstock' );
                }

                update_post_meta( $product_id, '_sale_price', $price );
                update_post_meta( $product_id, '_price', $price );

                // set product measurement
                update_post_meta( $product_id, '_length', $length );
                update_post_meta( $product_id, '_width', $width );
                update_post_meta( $product_id, '_height', $height );
                update_post_meta( $product_id, '_weight', $weight );
                update_post_meta( $product_id, '_unit', $unitOfMeasure );

                // set tag
                wp_set_object_terms( $product_id, $searchKeywords, 'product_tag', true );

                // Set product category
                wp_set_object_terms( $product_id, $type, 'product_cat' );


                // update product additional information
                update_post_meta( $product_id, '_jcolor', $color );
                update_post_meta( $product_id, '_type', $type );
                update_post_meta( $product_id, '_productID', $product_id );
                update_post_meta( $product_id, '_productNO', $no );
                update_post_meta( $product_id, '_Manufacturer', $manufacturers );
                update_post_meta( $product_id, '_Videos', $video_urls );
                update_post_meta( $product_id, '_Yield', $yield );
                update_post_meta( $product_id, '_Compatible_Printers', $printer_names );


                // set product gallery images
                if ( !empty( $images ) && is_array( $images ) ) {
                    foreach ( $images as $image_url ) {

                        // Extract image name
                        $image_name = basename( $image_url );
                        // Get WordPress upload directory
                        $upload_dir = wp_upload_dir();

                        // Download the image from URL and save it to the upload directory
                        $image_data = file_get_contents( $image_url );

                        if ( $image_data !== false ) {
                            $image_file = $upload_dir['path'] . '/' . $image_name;
                            file_put_contents( $image_file, $image_data );

                            // Prepare image data to be attached to the product
                            $file_path = $upload_dir['path'] . '/' . $image_name;
                            $file_name = basename( $file_path );

                            // Insert the image as an attachment
                            $attachment = [
                                'post_mime_type' => mime_content_type( $file_path ),
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                            ];

                            $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                            // Add the image to the product gallery
                            $gallery_ids   = get_post_meta( $product_id, '_product_image_gallery', true );
                            $gallery_ids   = explode( ',', $gallery_ids );
                            $gallery_ids[] = $attach_id;
                            update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );

                            set_post_thumbnail( $product_id, $attach_id );

                        }

                    }
                }

                return "<h3>Product Inserted Successfully</h3>";
            }
        }
    }
} catch (HttpClientException $e) {
    echo '<pre><code>' . print_r( $e->getMessage(), true ) . '</code><pre>'; // Error message.
    echo '<pre><code>' . print_r( $e->getRequest(), true ) . '</code><pre>'; // Last request data.
    echo '<pre><code>' . print_r( $e->getResponse(), true ) . '</code><pre>'; // Last response data.
}
add_shortcode( 'insert_product_to_woo', 'clover_product_insert_woocommerce' );

// insert product types via api
add_action( 'rest_api_init', 'insert_product_to_woo_via_api' );
function insert_product_to_woo_via_api() {
    register_rest_route(
        'cloverimaging/v1',
        '/product',
        [
            'methods'  => 'GET',
            'callback' => 'clover_product_insert_woocommerce',
        ]
    );
}

/**
 * Endpoint . replace with your endpoint
 * http://clover-api-integrate.test/wp-json/cloverimaging/v1/product
 */