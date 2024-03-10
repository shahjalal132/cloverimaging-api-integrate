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
        $consumer_key    = 'ck_99b0cc18ece4af1e7da02a49074bd9038527c852';
        $consumer_secret = 'cs_3c64f580e0433689e7207e747ce7adc5f43e6bbb';

        foreach ( $products as $product ) {

            // get operation_value from $product
            $product_data = $product->operation_value;

            // decode operation_value
            $product_data = json_decode( $product_data );

            // retrieve product data
            $sku          = $product_data->ID;
            $title        = $product_data->title;
            $no           = $product_data->no;
            $type         = $product_data->type;
            $color        = $product_data->color;
            $availability = $product_data->availability;

            // get images
            $images = $product_data->images ?? [];
            // set image limit 5
            $images = array_slice( $images, 0, 5 );

            $videos = $product_data->videos;

            // get attributes
            $attributes      = $product_data->attributes ?? [];
            $attribute_name  = '';
            $attribute_value = '';
            if ( !empty( $attributes ) && is_array( $attributes ) ) {
                foreach ( $attributes as $attribute ) {
                    $attribute_name .= $attribute->attributeName . '|';
                    $attribute_value .= $attribute->attributeValue . '|';
                }
            }

            $serviceLevels = $product_data->serviceLevels;
            // get price from serviceLevels
            $price = $serviceLevels[0]->price;
            // increase 30% price
            $price = round( $price * 1.3 );

            $compatiblePrinters = $product_data->compatiblePrinters;

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
                    'attributes'  => [
                        [
                            'name'        => 'Color',
                            'options'     => explode( '|', $color ),
                            'position'    => 0,
                            'visible'     => true,
                            'variation'   => true,
                            'is_taxonomy' => false,
                        ],
                    ],
                ];

                // update product
                $client->put( 'products/' . $product_id, $product_data_to_insert );

            } else {

                // Create a new product if not exists
                $product_data_to_insert = [
                    'name'        => $title,
                    'sku'         => "$sku",
                    'type'        => 'simple',
                    'description' => $description,
                    'attributes'  => [
                        [
                            'name'        => 'Color',
                            'options'     => explode( '|', $color ),
                            'position'    => 0,
                            'visible'     => true,
                            'variation'   => true,
                            'is_taxonomy' => false,
                        ],
                    ],
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


                // set product gallery images
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

                // Update the status of the processed product database
                $wpdb->update(
                    $table_name,
                    [ 'status' => 'completed' ],
                    [ 'id' => $product->id ]
                );

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