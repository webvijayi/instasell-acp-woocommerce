<?php
/**
 * Uninstall Instant Checkout via ACP Agentic Commerce for WooCommerce
 *
 * @package Instant_Checkout_ACP
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('icvaac_api_enabled');
delete_option('icvaac_stripe_publishable_key');
delete_option('icvaac_stripe_secret_key');
delete_option('icvaac_openai_api_key');
delete_option('icvaac_webhook_secret');
delete_option('icvaac_enable_logging');

// Delete transients/cache
delete_transient('icvaac_product_feed_cache');

// Remove custom post types
$posts = get_posts(
    array(
        'post_type' => 'acp_checkout_session',
        'numberposts' => -1,
        'post_status' => 'any'
    )
);

foreach ($posts as $post) {
    wp_delete_post($post->ID, true);
}

// Unregister post type
unregister_post_type('acp_checkout_session');

// Clear any cached data
wp_cache_flush();
