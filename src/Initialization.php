<?php

namespace WPCustomizableOptions;

/**
 * Class Initialization
 *
 * @package WPCustomizableOptions
 */
final class Initialization
{
    const KEY = 'customizable_options';
    const DOMAIN = 'customizable-options';
    /**
     * @var null|\WP_Customize_Manager
     */
    private $_customize = null;

    public function __construct()
    {
        add_action( 'customize_register', function ( $wp_customize ) {
            $this->_customize = $wp_customize;
            $this->_register();
        } );
        add_action( 'customize_controls_enqueue_scripts', function () {
            $this->_enqueue_customize_scripts();
        } );
        add_action( 'customize_preview_init', function () {
            $this->_enqueue_preview_scripts();
        } );
    }

    private function _register()
    {
        $this->_customize->add_section( static::KEY, [
            'title' => __( 'Customizable Options', static::DOMAIN )
        ] );
    }

    private function _enqueue_customize_scripts()
    {
        wp_enqueue_script( 'jquery' );

        static::add_script_settings( 'customize-controls', $this->get_script_settings() );
        static::add_inline_script( 'customize', 'customize-controls' );
    }
    
    private function _enqueue_preview_scripts()
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'underscore' );

        static::add_inline_script( 'jquery.caret.min', 'customize-preview' );
        static::add_script_settings( 'customize-preview', $this->get_script_settings() );
        static::add_inline_script( 'preview', 'customize-preview' );
    }
    
    private function get_script_settings()
    {
        return [
            'key' => static::KEY
        ];
    }

    public static function add_script_settings( $handle, array $settings = [] )
    {
        wp_add_inline_script(
            $handle,
            'var CustomizableOptions = ' . json_encode( $settings ) . ';',
            'before'
        );
    }

    /**
     * @param string $name
     * @param string $handle
     */
    public static function add_inline_script( $name, $handle )
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