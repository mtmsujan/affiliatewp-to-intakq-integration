<?php

namespace BOILERPLATE\Inc;

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;


class Admin_Menu {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // Add admin menu
        add_action( 'admin_menu', [ $this, 'admin_menu_callback' ] );

        // Add settings link to the plugin page
        add_filter( 'plugin_action_links_' . PLUGIN_BASENAME, [ $this, 'add_settings_link' ] );

        // Handle AJAX request for saving the API key
        add_action( 'wp_ajax_save_awpiq_api_key', [ $this, 'save_awpiq_api_key' ] );
    }

    public function admin_menu_callback() {
        add_menu_page(
            'awpiq_settings',
            'AWPIQ Settings',
            'manage_options',
            'awpiq_options_page',  // Make sure the slug is consistent
            [ $this, 'awpiq_settings_page_html' ],
            'dashicons-admin-generic',
            45
        );
    }

    public function awpiq_settings_page_html() {

        // Get the API key from the database
        $api_key = get_option( 'awpiq_api_key' );
        ?>

        <!-- Settings Page Heading -->
        <h1><?php _e( 'AWPIQ Settings', 'intakq' ); ?></h1>

        <div class="awpiq-wrapper" style="margin-top: 20px;">
            <label for="awpiq_api_key">API Key:</label>
            <input type="password" id="awpiq_api_key" name="awpiq_api_key" value="<?= esc_attr( $api_key ); ?>"
                placeholder="API Key">
            <button id="awpiq_save_api_key" class="button button-primary"><?php _e( 'Save API Key', 'intakq' ); ?></button>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#awpiq_save_api_key').on('click', function (e) {
                    e.preventDefault();

                    var api_key = $('#awpiq_api_key').val();

                    $.ajax({
                        url: ajaxurl, // WordPress AJAX URL
                        type: 'POST',
                        data: {
                            action: 'save_awpiq_api_key',
                            api_key: api_key,
                            _ajax_nonce: '<?php echo wp_create_nonce( "save_awpiq_api_key_nonce" ); ?>'
                        },
                        success: function (response) {
                            if (response.success) {
                                alert('API Key saved successfully!');
                            } else {
                                alert('Failed to save API Key!');
                            }
                        },
                        error: function () {
                            alert('Error occurred while saving the API Key.');
                        }
                    });
                });
            });
        </script>
        <?php
    }

    public function save_awpiq_api_key() {
        // Verify the nonce for security
        check_ajax_referer( 'save_awpiq_api_key_nonce' );

        // Check if the API key is provided
        if ( isset( $_POST['api_key'] ) ) {
            $api_key = sanitize_text_field( $_POST['api_key'] );

            // Save the API key to the options table
            update_option( 'awpiq_api_key', $api_key );

            // Return a success response
            wp_send_json_success( 'API Key saved successfully!' );
        } else {
            // Return an error if the API key is missing
            wp_send_json_error( 'API Key is missing.' );
        }
    }


    // Add the settings link to the plugin page
    public function add_settings_link( $links ) {
        // Add the custom settings link to your AWPIQ settings page
        $settings_link = '<a href="admin.php?page=awpiq_options_page">' . __( 'Settings', 'intakq' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}
