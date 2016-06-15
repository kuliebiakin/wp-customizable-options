<?php

if ( !function_exists( 'register_customizable_options' ) ) {
    /**
     * Initializes library
     */
    function register_customizable_options() {
        new \WPCustomizableOptions\Initialization();
    }
}

if ( !function_exists( '_the_customizable_text_value' ) ) {
    /**
     * Returns filtered text value
     *
     * @param $value
     *
     * @return bool|string
     */
    function _the_customizable_text_value( $value ) {
        return is_bool( $value ) ? $value : esc_html( $value );
    }

    /**
     * Apply filter to text value
     */
    add_filter( 'the_customizable_text_value', '_the_customizable_text_value' );
}

if ( !function_exists( 'the_customizable_text' ) ) {
    /**
     * Displays text which is editable in preview when admin is in Customizer
     * 
     * @param string      $option
     * @param string|null $default
     *
     * @return mixed
     */
    function the_customizable_text( $option, $default = null ) {
        global $wp_customize;

        $value = apply_filters( 'the_customizable_text_value', get_option( $option, $default ), $option );
        $tag = apply_filters( 'the_customizable_text_wrapper_tag', 'span', $option );

        echo !empty( $wp_customize )
            ? "<$tag data-" . \WPCustomizableOptions\Initialization::KEY . "=\"$option\">$value</$tag>" : $value;
    }
}