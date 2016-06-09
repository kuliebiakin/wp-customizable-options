<?php

namespace Customize_Preview_Edit;

/**
 * Class Initialization
 *
 * @package Customize_Preview_Edit
 */
final class Initialization
{
    public function __construct()
    {
        add_action( 'customize_controls_enqueue_scripts', function () {
            $this->_enqueue_customize_scripts();
        } );
        add_action( 'customize_preview_init', function () {
            $this->_enqueue_preview_scripts();
        } );
    }

    private function _enqueue_customize_scripts()
    {
        wp_enqueue_script( 'jquery' );

        $this->_add_inline_script( 'customize', 'customize-controls' );
    }
    
    private function _enqueue_preview_scripts()
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'underscore' );

        $this->_add_inline_script( 'jquery.caret.min', 'customize-preview' );
        $this->_add_inline_script( 'preview', 'customize-preview' );
    }

    /**
     * @param string $name
     * @param string $handle
     */
    private function _add_inline_script( $name, $handle )
    {
        wp_add_inline_script(
            $handle,
            static::get_script( $name )
        );
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function get_script( $name )
    {
        return file_get_contents( dirname( __DIR__ ) . "/assets/scripts/$name.js" );
    }
}