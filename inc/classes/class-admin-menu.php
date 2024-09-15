<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Admin_Menu {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'admin_menu', [ $this, 'admin_menu_callback' ] );
    }

    public function admin_menu_callback() {
        add_menu_page(
            'awpiq_settings',
            'AWPIQ Settings',
            'manage_options',
            'wporg_options_page',
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

}