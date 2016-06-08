<?php

namespace Customize_Preview_Edit;

/**
 * Class Initialization
 *
 * @package Customize_Preview_Edit
 */
final class Initialization
{
    const VERSION = '0.0.1';
    
    public function __construct()
    {
        add_action( 'customize_controls_enqueue_scripts', function () {
            $this->_enqueue_customize_scripts();
        }, 999 );
        add_action( 'customize_preview_init', function () {
            $this->_enqueue_preview_scripts();
        }, 999 );
    }

    private function _enqueue_customize_scripts()
    {
        wp_enqueue_script(
            'customize-preview-edit-admin',
            plugins_url( '/customize-preview-edit/assets/scripts/customize.js' ),
            [ 'jquery' ],
            static::VERSION,
            true
        );
    }
    
    private function _enqueue_preview_scripts()
    {
        wp_enqueue_script( 
            'customize-preview-edit-frontend',
            plugins_url( '/customize-preview-edit/assets/scripts/preview.js' ), 
            [ 'jquery', 'underscore', 'customize-preview' ],
            static::VERSION,
            true
        );
    }
}