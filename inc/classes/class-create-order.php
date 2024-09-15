<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Create_Order {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // add_action( 'woocommerce_payment_complete', [ $this, 'create_order' ] ); // Trigger after payment completion
        add_action( 'woocommerce_thankyou', [ $this, 'create_order' ] );
    }

    public function create_order( $order_id ) {
        // Get the order object
        $order = wc_get_order( $order_id );

        if ( !$order ) {
            $this->put_program_logs( 'Order not found.' );
            return;
        }

        // Prepare the data array dynamically from the order
        $data = [
            'ClientId'  => 0,
            'Name'      => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'FirstName' => $order->get_billing_first_name(),
            'LastName'  => $order->get_billing_last_name(),
            'Email'     => $order->get_billing_email(),
            'Phone'     => $order->get_billing_phone(),
        ];

        // Log the payload
        // $this->put_program_logs( 'Payload: ' . json_encode( $data ) );

        // Create order and call API
        $response = $this->call_api( $data );

        // Log the API response
        $this->put_program_logs( 'API Response: ' . $response );
    }

    public function call_api( $data ) {

        $curl = curl_init();

        curl_setopt_array( $curl, array(
            CURLOPT_URL            => 'https://intakeq.com/api/v1/clients',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode( $data ), // Dynamically pass $data array as JSON payload
            CURLOPT_HTTPHEADER     => array(
                'X-Auth-Key: ',
                'Content-Type: application/json',
            ),
        ) );

        $response = curl_exec( $curl );

        if ( curl_errno( $curl ) ) {
            $error_msg = curl_error( $curl );
            $this->put_program_logs( 'cURL error: ' . $error_msg );
        }

        curl_close( $curl );

        return $response;
    }
}
