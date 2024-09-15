<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Modify_Checkout_Form {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_filter( 'woocommerce_checkout_fields', [ $this, 'custom_override_checkout_fields' ] );
    }

    public function custom_override_checkout_fields( $fields ) {

        // Unset the company name field
        unset( $fields['billing']['billing_company'] );

        // Unset billing address fields
        unset( $fields['billing']['billing_address_1'] );
        unset( $fields['billing']['billing_address_2'] );
        unset( $fields['billing']['billing_city'] );
        unset( $fields['billing']['billing_postcode'] );
        unset( $fields['billing']['billing_state'] );
        unset( $fields['billing']['billing_country'] );

        return $fields;
    }
}