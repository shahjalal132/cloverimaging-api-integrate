<?php

/**
 * Fetch invoices form api and insert to database
 *
 * @return string
 */
function fetch_invoices_api() {
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://www.cloverimaging.com/access-point/invoices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'Authorization: Bearer iqgSOfSAlbis8MKg3w2ZTlskuYdIkQyT95zVLt7685qDUh2mjlUYJS37PQmNsrE5RfIzL1KEIw1yoKnKzGacVvDXtLTcp0Xx1NZkDYRdAa4EhptoumcWUhwiafeoFGTPQO4N6xRKw0vcGAZ2BpHXI2RJAO9ZF8WPW8OrReFbHvJ3bqk9I8dp74rBB4ZjkpnPTqoWVezHDXa0HLSlPGbCtD3vjiXnYwLJaOiB9oyGv5Qg2UNV31N7h7zugSfb0Hl',
                'Cookie: ApplicationGatewayAffinity=b30938de242284bc5271249ca994a1d5; ApplicationGatewayAffinityCORS=b30938de242284bc5271249ca994a1d5; cigusaweb=okve7316hj52pkpmptqnso2g5ndfqgld; exit_intent_popup=1',
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

function insert_invoices_to_db() {
    ob_start();

    // get api response
    $response = fetch_invoices_api();

    // put invoices to local file
    put_invoices_response_data( $response );

    // decode api response
    $response = json_decode( $response );

    echo '<pre>';
    print_r( $response );
    wp_die();

    return ob_get_clean();
}

add_shortcode( 'fetch_invoices', 'insert_invoices_to_db' );