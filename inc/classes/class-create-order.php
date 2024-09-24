<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Create_Order {

    use Singleton;
    use Program_Logs;

    private $product_id;
    private $package_id;
    private $package_info;
    private $package_title;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'woocommerce_payment_complete', [ $this, 'create_order' ] ); // Trigger after payment completion
        // add_action( 'woocommerce_thankyou', [ $this, 'create_order' ] );
    }

    public function create_order( $order_id ) {

        // Get the order object
        $order = wc_get_order( $order_id );

        if ( !$order ) {
            update_option( 'order_not_found', 'Order not Found' );
            return;
        }

        // get order total
        $order_total = $order->get_total();
        // get payment status
        $payment_status = 'paid';

        // Get items
        $items = $order->get_items();
        if ( $items ) {
            foreach ( $items as $item_id => $item ) {
                // Get product ID
                $this->product_id = $item->get_product_id();
                // Get product title
                $this->package_title = $item->get_name();
            }
        }

        // Get package info from postmeta table by _intakq_page_options key
        // $this->package_info = get_post_meta( $this->product_id, '_intakq_page_options', true );
        // Get package ID
        // $this->package_id = $this->package_info['packageId'];

        // Generate additional information
        $additional_information = sprintf( "Package: %s | Price: %s | Status: %s", $this->package_title, $order_total, $payment_status );

        // Prepare the basic data array dynamically from the order
        $mandatory_data = [
            'ClientId'  => 0,
            'Name'      => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'FirstName' => $order->get_billing_first_name(),
            'LastName'  => $order->get_billing_last_name(),
            'Email'     => $order->get_billing_email(),
            'Phone'     => $order->get_billing_phone(),
        ];

        // Prepare the additional data array based on the full payload structure
        $additional_data = [
            'MiddleName'                          => null,
            'DateOfBirth'                         => null,
            'MaritalStatus'                       => null,
            'Gender'                              => null,
            'Archived'                            => false,
            'HomePhone'                           => $order->get_billing_phone(),
            'WorkPhone'                           => null,
            'MobilePhone'                         => null,
            'Address'                             => $order->get_billing_address_1(),
            'UnitNumber'                          => null,
            'StreetAddress'                       => $order->get_billing_address_1(),
            'City'                                => $order->get_billing_city(),
            'StateShort'                          => $order->get_billing_state(),
            'Country'                             => $order->get_billing_country(),
            'PostalCode'                          => $order->get_billing_postcode(),
            'PractitionerId'                      => null,
            'AdditionalInformation'               => $additional_information,
            'PrimaryInsuranceCompany'             => null,
            'PrimaryInsurancePolicyNumber'        => null,
            'PrimaryInsuranceGroupNumber'         => null,
            'PrimaryInsuranceHolderName'          => null,
            'PrimaryInsuranceRelationship'        => null,
            'PrimaryInsuranceHolderDateOfBirth'   => null,
            'SecondaryInsuranceCompany'           => null,
            'SecondaryInsurancePolicyNumber'      => null,
            'SecondaryInsuranceGroupNumber'       => null,
            'SecondaryInsuranceHolderName'        => null,
            'SecondaryInsuranceRelationship'      => null,
            'SecondaryInsuranceHolderDateOfBirth' => null,
            'DateCreated'                         => time() * 1000, // Current timestamp in milliseconds
            'LastActivityDate'                    => time() * 1000,
            'StripeCustomerId'                    => null,
            'SquareCustomerId'                    => null,
            'ExternalClientId'                    => null,
            'CustomFields'                        => [],
        ];

        // Merge the additional data into the main $data array
        $payload = array_merge( $mandatory_data, $additional_data );

        // Put payload to log
        // $this->put_program_logs( 'Payload: ' . json_encode( $payload ) );

        // Create order and call API
        $response = $this->call_api( $payload );

        // Put response to log
        // $this->put_program_logs( 'Response: ' . $response );

        // Log the API response
        update_option( 'api_response', $response );
    }


    public function call_api( $data ) {

        // get API key from database
        $api_key = get_option( 'awpiq_api_key' );

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
            CURLOPT_POSTFIELDS     => json_encode( $data ),
            CURLOPT_HTTPHEADER     => array(
                'X-Auth-Key: ' . $api_key,
                'Content-Type: application/json',
            ),
        ) );

        $response = curl_exec( $curl );

        if ( curl_errno( $curl ) ) {
            $error_msg = curl_error( $curl );
            // $this->put_program_logs( 'cURL error: ' . $error_msg );
            update_option( 'curl_error', $error_msg );
        }

        curl_close( $curl );

        return $response;
    }
}
