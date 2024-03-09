<?php

/**
 * Fetch all products from api and insert to database
 *
 * @return string
 */
function fetch_all_products_api( $page = 1 ) {
    $curl = curl_init();
    $data = [
        'page' => $page,
    ];
    $data = json_encode( $data );

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
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => array(
                'Authorization: Bearer aGT2RlvMpTdLPA8FYmCqPBe6v1gMVFmC5LrGra6fufk3uE9JKKag7B2JYVZKI5iSSv0HSNQrWtFW0T8Y2DNlxs44GozyxERUqf5xZsJjJMfhDbldAbW6NTyX7rqywjAVFDXb8Lf8sztcQHRJh7TCzhbsw9MZxt8XBiHtApdAP1Ucc54ylaXoB0iN1n4QiNMOivroKOSRH50cwwjE40C3nbuqlzZLQdktzXgj2U79s7WYpc2vnIe93IjDwE3kBpg',
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

    for ( $i = 1; $i <= 65; $i++ ) {
        // get api response
        $response = fetch_all_products_api( $i );

        // put api response to local file
        put_products_response_data( $response );

        // decode api response
        $response = json_decode( $response, true );

        // get products array
        $products = $response['products'];

        // Insert to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'sync_clover_products';
        // $wpdb->query( "TRUNCATE TABLE $table_name" );

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
    }

    return ob_get_clean();
}

// insert products via shortcode
add_shortcode( 'insert_products', 'insert_product_to_db' );

// insert product types via api
add_action( 'rest_api_init', 'insert_product_to_db_via_api' );
function insert_product_to_db_via_api() {
    register_rest_route(
        'cloverimaging/v1',
        '/product-types',
        [
            'methods'  => 'GET',
            'callback' => 'insert_product_to_db',
        ]
    );
}