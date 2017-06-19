<?php
global $options;
class Transport {

	public static function deliver($endpoint = null, $payload = array(), $method = "GET") {

        $path = rtrim($GLOBALS['settings']['url'], '/') . '/';

        if ($method == "GET") {
            $resp = wp_remote_post($path . $endpoint, array(
                'method' => "GET",
                'headers' => array(
                    'Content-type' => 'application/json',
                    'Authorization' => 'JWT ' . $GLOBALS['settings']['token']
                )
            ));
        } else {
            $resp = wp_remote_post($path . $endpoint, array(
                'method' => $method,
                'timeout' => 10,
                'headers' => array(
                    'Content-type' => 'application/json',
                    'Authorization' => 'JWT ' . $GLOBALS['settings']['token']
                ),
                'body' => json_encode($payload)
            ));
        }

        if ( is_wp_error( $resp ) ) {
            $error_message = $resp->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            if ($resp['response']['code'] != 201 && $resp['response']['code'] != 200) {
                header('Content-Type: application/json');
                echo ('An error seems to have occurred.');
                print_r( $resp );
            } else {
                return $resp;
            }
        }

	}

}
