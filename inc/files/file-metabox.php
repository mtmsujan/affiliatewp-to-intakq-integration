<?php

if ( !defined( 'ABSPATH' ) ) {
    die;
} // Cannot access directly.

/**
 * Metabox of the PAGE
 * Set a unique slug-like ID
 */
$prefix_page_opts = '_intakq_page_options';

/**
 * Create a metabox
 */
CSF::createMetabox( $prefix_page_opts, array(
    'title'        => 'Package information\'s',
    'post_type'    => 'product',
    'show_restore' => true,
) );

/**
 * Create a section
 */
CSF::createSection( $prefix_page_opts, array(
    'title'  => 'Package information',
    'icon'   => 'fas fa-info',
    'fields' => array(

        // service id
        array(
            'id'          => 'packageId',
            'type'        => 'text',
            'title'       => 'Package ID',
            'placeholder' => 'Enter Package ID',
        ),
    ),
) );