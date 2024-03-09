<?php

/**
 * Fetch all product types
 *
 * @return string
 */
function get_all_product_types_api() {
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://www.cloverimaging.com/access-point/get-all-product-types',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => '{
  "apiKey":"5pzd3s2jgt1ks2X1GRareNnJEhJcq4myx3CN8lpCaoLCkIz0Iz8mUnGlLIbaXQk6MzoqkJ9j21AxqZmb29E2o2lKbvvEGchBrIYjG36gpircmNRQ2mi1UINc8xtn0bnCTspQhg5tonoPvpco3sKQELcsa157NqShydut2YgJBG9Kv6ayvBU2ergOJZv0MSTTpveJrHlEL7kgb6y0qhg228iGjG9EwFeDBxxdR9aQedNN9but77ifK10pLQ"
}',
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Authorization: Bearer ydEtGoLn4FZiQSTeTK9hfDvT7LVfcd6rvcBAxgKlbw25bnwOAHzx12kYsF35JdXJREItGjIj4v8so9Z3pWCFQyxg1rDYtcfN7liIuY7VqgqAZnVX67eHeEGiIDcGSauskWC3X5Hu84ImLadJiB2AFyNDkmbgHsP6vuhO7aC06xRU9MkeBmY4sBypU5iHorEO1cDpKQz8VyfYPgfdLmVNoMnRh0r1FK4XjqCpMUWPQq8xXmS0hoG0UjEpaLOzrjZ',
                'Cookie: ApplicationGatewayAffinity=b30938de242284bc5271249ca994a1d5; ApplicationGatewayAffinityCORS=b30938de242284bc5271249ca994a1d5; cigusaweb=okve7316hj52pkpmptqnso2g5ndfqgld; exit_intent_popup=1',
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

function insert_product_types_to_db() {
    ob_start();

    // get api response
    $response = get_all_product_types_api();

    // truncate table
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_clover_product_types';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    // put api response to local file
    put_product_types_response_data( $response );

    // insert product types
    $wpdb->insert(
        $table_name,
        [
            'operation_type'  => 'product_types',
            'operation_value' => $response,
        ]
    );

    echo '<h4>Product types inserted successfully</h4>';

    return ob_get_clean();
}

// insert product types via shortcode
add_shortcode( 'insert_product_types', 'insert_product_types_to_db' );

// insert product types via api
add_action( 'rest_api_init', 'insert_product_to_woo' );
function insert_product_to_woo() {
    register_rest_route(
        'cloverimaging/v1',
        '/product-types',
        [
            'methods'  => 'GET',
            'callback' => 'insert_product_types',
        ]
    );
}


/**
 * Fetch all products from api and insert to database
 *
 * @return string
 */
function fetch_all_products_api() {
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://www.cloverimaging.com/access-point/products',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'Authorization: Bearer ydEtGoLn4FZiQSTeTK9hfDvT7LVfcd6rvcBAxgKlbw25bnwOAHzx12kYsF35JdXJREItGjIj4v8so9Z3pWCFQyxg1rDYtcfN7liIuY7VqgqAZnVX67eHeEGiIDcGSauskWC3X5Hu84ImLadJiB2AFyNDkmbgHsP6vuhO7aC06xRU9MkeBmY4sBypU5iHorEO1cDpKQz8VyfYPgfdLmVNoMnRh0r1FK4XjqCpMUWPQq8xXmS0hoG0UjEpaLOzrjZ',
                'Cookie: ApplicationGatewayAffinity=b30938de242284bc5271249ca994a1d5; ApplicationGatewayAffinityCORS=b30938de242284bc5271249ca994a1d5; cigusaweb=9mcebtouso609o0hdqgl3v1qe5niphoq; exit_intent_popup=1',
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

function insert_product_to_db() {
    ob_start();

    // get api response
    $response = fetch_all_products_api();

    // put api response to local file
    put_products_response_data( $response );

    // decode api response
    $response = json_decode( $response, true );

    // get products array
    $products = $response['products'];

    // Insert to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_clover_products';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    if ( !empty( $products ) && is_array( $products ) ) {
        foreach ( $products as $product ) {
            // encode to json
            $product = json_encode( $product );

            // insert product
            $wpdb->insert(
                $table_name,
                [
                    'operation_type'  => 'product',
                    'operation_value' => $product,
                    'status'          => 'pending',
                ]
            );
        }
    }

    echo '<h4>Products inserted successfully</h4>';

    return ob_get_clean();
}

// insert products via shortcode
add_shortcode( 'insert_products', 'insert_product_to_db' );