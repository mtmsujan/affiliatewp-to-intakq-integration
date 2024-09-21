<?php
/**
 * Bootstraps the plugin. load class.
 */

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Singleton;

class Autoloader {
    use Singleton;

    protected function __construct() {

        // load class.
        I18n::get_instance();
        Enqueue_Assets::get_instance();
        Modify_Checkout_Form::get_instance();
        Create_Order::get_instance();
        Admin_Menu::get_instance();
        File_Upload::get_instance();
    }
}