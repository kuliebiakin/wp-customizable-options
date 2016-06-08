<?php

namespace Customize_Preview_Edit;

/**
 * Class Functions
 *
 * @package Customize_Preview_Edit
 */
final class Functions
{
    /**
     * @param string $option
     * @param string $default
     *
     * @return mixed
     */
    public static function get_option( $option, $default = '...' )
    {
        global $wp_customize;

        $value = get_option( $option, $default );

        return !empty( $wp_customize )
            ? "<span data-customize-preview-edit=\"$option\">$value</span>" : $value;
    }
}