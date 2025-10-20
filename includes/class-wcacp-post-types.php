<?php
/**
 * Post Types for ACP
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WCACP_Post_Types
 */
class WCACP_Post_Types {

    /**
     * Initialize the post types
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
    }

    /**
     * Register post types
     */
    public static function register_post_types() {
        register_post_type('acp_checkout_session',
            array(
                'labels' => array(
                    'name' => __('ACP Checkout Sessions', 'acp-instant-checkout-for-woocommerce'),
                    'singular_name' => __('ACP Checkout Session', 'acp-instant-checkout-for-woocommerce'),
                ),
                'public' => false,
                'has_archive' => false,
                'rewrite' => array('slug' => 'acp-checkout-sessions'),
                'supports' => array('title', 'custom-fields'),
            )
        );
    }
}

WCACP_Post_Types::init();
