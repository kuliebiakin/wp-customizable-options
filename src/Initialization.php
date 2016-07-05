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
            $this->_enqueue_preview_styles();
            $this->_enqueue_preview_scripts();
        } );
        add_action( 'customize_controls_print_footer_scripts', function () {
            $this->_render_tmpl();
        } );
        add_action( 'customize_save', function () {
            $this->_save();
        } );
        add_filter( 'customize_dynamic_setting_args', function ( $setting_args ) {
            return !empty( $setting_args ) && is_array( $setting_args )
                ? array_merge( $setting_args, $this->_get_default_setting_args() ) : $this->_get_default_setting_args();
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

        $this->_enqueue_script_settings( 'customize-controls' );
        static::add_inline_script( 'customize', 'customize-controls' );
    }

    private function _enqueue_preview_styles()
    {
        wp_enqueue_style( 'dashicons' );
    }
    
    private function _enqueue_preview_scripts()
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'underscore' );

        static::add_inline_script( 'jquery.caret.min', 'customize-preview' );
        $this->_enqueue_script_settings( 'customize-preview' );
        static::add_inline_script( 'preview', 'customize-preview' );
    }

    private function _enqueue_script_settings( $handle )
    {
        static::add_script_settings( $handle, 'CustomizableOptions', $this->_get_script_settings() );
    }

    /**
     * @return array
     */
    private function _get_script_settings()
    {
        return [
            'key'     => static::KEY,
            'setting' => [
                'default' => $this->_get_default_setting_args()
            ]
        ];
    }

    /**
     * @return array
     */
    private function _get_default_setting_args()
    {
        return [
            'type'      => 'option',
            'transport' => 'postMessage'
        ];
    }

    private function _render_tmpl()
    {
        static::add_tmpl( 'text', function () {
            ?>
            <li id="customize-control-{{ data.setting }}" class="customize-control customize-control-{{ data.type || 'text' }}">
                <label>
                    <# if (data.label) { #>
                        <span class="customize-control-title">{{ data.label }}</span>
                    <# }
                    if (data.description) { #>
                        <span class="description customize-control-description">{{{ data.description }}}</span>
                    <# } #>
                    <input type="{{ data.type || 'text' }}" value="{{ data.value }}" data-customize-setting-link="{{ data.setting }}" />
                </label>
            </li>
            <?php
        } );
    }

    private function _save()
    {
        $post_values = $this->_customize->unsanitized_post_values();
        $key = '_' . static::KEY;

        if ( isset( $post_values[ $key ] ) ) {
            $this->_customize->add_dynamic_settings( array_map( 'sanitize_key', $post_values[ $key ] ) );
        }
    }

    /**
     * @param string $handle
     * @param string $name
     * @param array  $settings
     */
    public static function add_script_settings( $handle, $name, array $settings = [] )
    {
        wp_add_inline_script(
            $handle,
            "window.$name = " . json_encode( $settings ) . ';',
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

    /**
     * @param string   $type
     * @param callable $tmpl
     */
    public static function add_tmpl( $type, callable $tmpl )
    {
        ?>
        <script type="text/html" id="tmpl-customize-control-<?= static::KEY ?>-<?= $type ?>">
            <?php $tmpl() ?>
        </script>
        <?php
    }
}