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
        ?>
        <h1><?php _e( 'AWPIQ Settings', 'intakq' ); ?></h1>
        <?php
    }

    // Add the settings link to the plugin page
    public function add_settings_link( $links ) {
        // Add the custom settings link to your AWPIQ settings page
        $settings_link = '<a href="admin.php?page=awpiq_options_page">' . __( 'Settings', 'intakq' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}
