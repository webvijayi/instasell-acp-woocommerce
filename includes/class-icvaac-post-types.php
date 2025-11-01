<?php
/**
 * Post Types for ACP
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ICVAAC_Post_Types
 */
class ICVAAC_Post_Types {

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
                    'name' => __('ACP Checkout Sessions', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
                    'singular_name' => __('ACP Checkout Session', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
                ),
                'public' => false,
                'has_archive' => false,
                'rewrite' => array('slug' => 'acp-checkout-sessions'),
                'supports' => array('title', 'custom-fields'),
            )
        );
    }
}

ICVAAC_Post_Types::init();
