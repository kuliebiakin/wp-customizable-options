<?php

if ( !function_exists( 'the_customizable_text' ) ) {
    /**
     * Displays text which is editable in preview when admin is in Customizer
     * 
     * @param string      $option
     * @param string|null $default
     * @param string      $tag
     *
     * @return mixed
     */
    function the_customizable_text( $option, $default = null, $tag = 'span' ) {
        global $wp_customize;

        $value = get_option( $option, $default );

        echo !empty( $wp_customize )
            ? "<$tag data-" . \WPCustomizableOptions\Initialization::KEY . "=\"$option\">$value</$tag>" : $value;
    }
}
