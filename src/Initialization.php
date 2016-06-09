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
    /**
     * @var null|string
     */
    protected $_assets_url = null;
    
    public function __construct( $base_url )
    {
        $this->_assets_url = "$base_url/customize-preview-edit/assets";
        
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
            "{$this->_assets_url}/scripts/customize.js",
            [ 'jquery' ],
            static::VERSION,
            true
        );
    }
    
    private function _enqueue_preview_scripts()
    {
        wp_enqueue_script(
            'jquery-caret',
            "{$this->_assets_url}/scripts/jquery.caret.min.js",
            [ 'jquery' ],
            '0.3.1',
            true
        );
        wp_enqueue_script(
            'customize-preview-edit-frontend',
            "{$this->_assets_url}/scripts/preview.js",
            [ 'jquery', 'jquery-caret', 'underscore', 'customize-preview' ],
            static::VERSION,
            true
        );
    }
}