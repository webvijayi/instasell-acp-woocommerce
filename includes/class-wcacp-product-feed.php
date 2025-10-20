<?php
/**
 * Product Feed Generator for ACP
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WCACP_Product_Feed
 */
class WCACP_Product_Feed {

    /**
     * Plugin instance
     */
    private $plugin;

    /**
     * Constructor
     */
    public function __construct() {
        $this->plugin = woocommerce_acp_instant_checkout();
        $this->init();
    }

    /**
     * Initialize the product feed generator
     */
    private function init() {
        // Add endpoint for product feed
        add_action('rest_api_init', array($this, 'register_feed_endpoint'));

        // Schedule feed updates
        add_action('wcacp_update_product_feed', array($this, 'update_feed_cache'));

        // Hook into product updates to invalidate cache
        add_action('woocommerce_update_product', array($this, 'invalidate_feed_cache'));
        add_action('woocommerce_new_product', array($this, 'invalidate_feed_cache'));
        add_action('woocommerce_trash_product', array($this, 'invalidate_feed_cache'));
    }

    /**
     * Register feed REST endpoint
     */
    public function register_feed_endpoint() {
        register_rest_route(
            'wcacp/v1',
            '/product-feed',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_product_feed'),
                'permission_callback' => array($this, 'validate_feed_request'),
                'args' => array(
                    'format' => array(
                        'default' => 'json',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'limit' => array(
                        'default' => 1000,
                        'sanitize_callback' => 'absint',
                    ),
                    'offset' => array(
                        'default' => 0,
                        'sanitize_callback' => 'absint',
                    ),
                ),
            )
        );
    }

    /**
     * Validate feed request
     */
    public function validate_feed_request($request) {
        // For now, allow public access to feed
        // In production, you might want to add API key validation
        return true;
    }

    /**
     * Get product feed
     */
    public function get_product_feed($request) {
        $format = $request->get_param('format');
        $limit = $request->get_param('limit');
        $offset = $request->get_param('offset');

        // Get cached feed or generate new one
        $cache_key = 'wcacp_product_feed_' . md5($format . $limit . $offset);
        $cached_feed = get_transient($cache_key);

        if ($cached_feed !== false) {
            return new WP_REST_Response(json_decode($cached_feed, true), 200);
        }

        // Generate feed
        $products = $this->get_products_for_feed($limit, $offset);
        $feed_data = array();

        foreach ($products as $product) {
            $feed_data[] = $this->format_product_for_feed($product, $format);
        }

        // Cache the feed for 15 minutes
        set_transient($cache_key, wp_json_encode($feed_data), 15 * MINUTE_IN_SECONDS);

        return new WP_REST_Response($feed_data, 200);
    }

    /**
     * Get products for feed
     */
    private function get_products_for_feed($limit, $offset) {
        $args = array(
            'status' => 'publish',
            'type' => array('simple', 'variable'),
            'limit' => $limit,
            'offset' => $offset,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );

        return wc_get_products($args);
    }

    /**
     * Format product for feed
     */
    private function format_product_for_feed($product, $format = 'json') {
        $product_data = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'description' => $product->get_description(),
            'short_description' => $product->get_short_description(),
            'sku' => $product->get_sku(),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'currency' => get_woocommerce_currency(),
            'stock_status' => $product->get_stock_status(),
            'stock_quantity' => $product->get_stock_quantity(),
            'manage_stock' => $product->get_manage_stock(),
            'is_virtual' => $product->is_virtual(),
            'is_downloadable' => $product->is_downloadable(),
            'categories' => wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names')),
            'tags' => wp_get_post_terms($product->get_id(), 'product_tag', array('fields' => 'names')),
            'images' => array(),
            'attributes' => array(),
            'permalink' => $product->get_permalink(),
            'created_at' => $product->get_date_created()->date('Y-m-d\TH:i:s\Z'),
            'updated_at' => $product->get_date_modified()->date('Y-m-d\TH:i:s\Z'),
        );

        // Add images
        $image_id = $product->get_image_id();
        if ($image_id) {
            $image_url = wp_get_attachment_url($image_id);
            $product_data['images'][] = array(
                'id' => $image_id,
                'url' => $image_url,
                'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true)
            );
        }

        // Add gallery images
        $gallery_image_ids = $product->get_gallery_image_ids();
        foreach ($gallery_image_ids as $gallery_image_id) {
            $gallery_image_url = wp_get_attachment_url($gallery_image_id);
            $product_data['images'][] = array(
                'id' => $gallery_image_id,
                'url' => $gallery_image_url,
                'alt' => get_post_meta($gallery_image_id, '_wp_attachment_image_alt', true)
            );
        }

        // Add attributes for variable products
        if ($product->is_type('variable')) {
            $attributes = $product->get_variation_attributes();
            foreach ($attributes as $attribute_name => $attribute_values) {
                $product_data['attributes'][] = array(
                    'name' => $attribute_name,
                    'values' => $attribute_values,
                    'visible' => true
                );
            }
        }

        return $product_data;
    }

    /**
     * Update feed cache
     */
    public function update_feed_cache() {
        // Clear existing feed cache
        $this->clear_feed_cache();

        // Generate new cache by making a test request
        $test_request = new WP_REST_Request('GET', '/wcacp/v1/product-feed');
        $test_request->set_param('limit', 100);
        $test_request->set_param('offset', 0);

        $response = $this->get_product_feed($test_request);

        // Schedule next update in 15 minutes
        wp_schedule_single_event(time() + 15 * MINUTE_IN_SECONDS, 'wcacp_update_product_feed');
    }

    /**
     * Clear feed cache
     */
    private function clear_feed_cache() {
        global $wpdb;

        // Get all transient keys from cache first
        $cache_group = 'wcacp_feed_transients';
        $cached_keys = wp_cache_get('transient_keys', $cache_group);
        
        if ($cached_keys && is_array($cached_keys)) {
            foreach ($cached_keys as $key) {
                delete_transient($key);
            }
            wp_cache_delete('transient_keys', $cache_group);
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Bulk deletion of transients, caching not applicable
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_wcacp_product_feed_') . '%'
            )
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Bulk deletion of transients, caching not applicable
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_timeout_wcacp_product_feed_') . '%'
            )
        );
    }

    /**
     * Invalidate feed cache when products are updated
     */
    public function invalidate_feed_cache($product_id = null) {
        $this->clear_feed_cache();

        // Schedule immediate cache update
        wp_schedule_single_event(time() + 60, 'wcacp_update_product_feed');
    }

    /**
     * Generate TSV feed (alternative format)
     */
    private function generate_tsv_feed($products) {
        $output = "id\tname\tdescription\tprice\tcurrency\tstock_status\tpermalink\n";

        foreach ($products as $product) {
            $output .= sprintf(
                "%d\t%s\t%s\t%s\t%s\t%s\t%s\n",
                $product->get_id(),
                $product->get_name(),
                wp_strip_all_tags($product->get_short_description()),
                $product->get_price(),
                get_woocommerce_currency(),
                $product->get_stock_status(),
                $product->get_permalink()
            );
        }

        return $output;
    }

    /**
     * Generate CSV feed (alternative format)
     */
    private function generate_csv_feed($products) {
        $output = "id,name,description,price,currency,stock_status,permalink\n";

        foreach ($products as $product) {
            $output .= sprintf(
                "%d,\"%s\",\"%s\",%s,%s,%s,\"%s\"\n",
                $product->get_id(),
                str_replace('"', '""', $product->get_name()),
                str_replace('"', '""', wp_strip_all_tags($product->get_short_description())),
                $product->get_price(),
                get_woocommerce_currency(),
                $product->get_stock_status(),
                $product->get_permalink()
            );
        }

        return $output;
    }
}
