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

        // Modify checkout fields
        add_filter( 'woocommerce_checkout_fields', [ $this, 'custom_override_checkout_fields' ] );

        // Change add to cart button text on single product page
        add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'custom_single_add_to_cart_text' ] );

        // Change add to cart button text on shop and archive pages
        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'custom_archive_add_to_cart_text' ] );

        // Redirect to checkout page after adding a product to the cart
        add_filter( 'woocommerce_add_to_cart_redirect', [ $this, 'custom_redirect_to_checkout' ] );

        // Replace button text
        add_filter( 'woocommerce_order_button_text', [ $this, 'custom_woocommerce_order_button_text' ] );
    }

    public function custom_override_checkout_fields( $fields ) {

        // Unset the company name field
        unset( $fields['billing']['billing_company'] );

        // Unset billing address fields
        // unset( $fields['billing']['billing_address_1'] );
        unset( $fields['billing']['billing_address_2'] );
        // unset( $fields['billing']['billing_city'] );
        // unset( $fields['billing']['billing_postcode'] );
        // unset( $fields['billing']['billing_state'] );
        // unset( $fields['billing']['billing_country'] );

        return $fields;
    }

    public function custom_single_add_to_cart_text() {
        return __( 'Sign Up', 'intakq' );
    }

    public function custom_archive_add_to_cart_text() {
        return __( 'Sign Up', 'intakq' );
    }

    public function custom_redirect_to_checkout() {
        return wc_get_checkout_url();
    }

    public function custom_woocommerce_order_button_text() {
        return __( 'Sign Up', 'intakq' );
    }
}