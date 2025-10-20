<?php
/**
 * Plugin Name: WooCommerce (OpenAI - Buy in ChatGPT) ACP Instant Checkout Plugin
 * Plugin URI:  https://github.com/lmotwani/woocommerce-acp-instant-checkout
 * Description: Enables OpenAI's Agentic Commerce Protocol (ACP) for any WooCommerce store, allowing "Buy it in ChatGPT" functionality.
 * Version:     1.0.0
 * Author:      Lokesh Motwani
 * Author URI:  https://LokeshMotwani.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: acp-woocommerce-ultraproduction
 * Domain Path: /languages
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Main plugin class
final class WooCommerce_ACP_Instant_Checkout {

    /**
     * Plugin instance.
     *
     * @var WooCommerce_ACP_Instant_Checkout
     */
    private static $instance;

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Constructor.
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define constants.
     */
    private function define_constants() {
        define('WCACP_PLUGIN_FILE', __FILE__);
        define('WCACP_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('WCACP_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('WCACP_VERSION', $this->version);
    }

    /**
     * Include required files.
     */
    private function includes() {
        // Load Composer autoloader if available
        if (file_exists(WCACP_PLUGIN_DIR . 'vendor/autoload.php')) {
            require_once WCACP_PLUGIN_DIR . 'vendor/autoload.php';
        }

        require_once WCACP_PLUGIN_DIR . 'includes/class-wcacp-admin.php';
        require_once WCACP_PLUGIN_DIR . 'includes/class-wcacp-api-endpoints.php';
        require_once WCACP_PLUGIN_DIR . 'includes/class-wcacp-checkout-session.php';
        require_once WCACP_PLUGIN_DIR . 'includes/class-wcacp-product-feed.php';
        require_once WCACP_PLUGIN_DIR . 'includes/class-wcacp-post-types.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'));
        add_action('init', array($this, 'load_textdomain'));
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain('acp-woocommerce-ultraproduction', false, dirname(plugin_basename(WCACP_PLUGIN_FILE)) . '/languages/');
    }

    /**
     * On plugins loaded.
     */
    public function on_plugins_loaded() {
        // Initialization
        new WCACP_Admin();
        new WCACP_Product_Feed();

        // Register API endpoints
        $api_endpoints = new WCACP_API_Endpoints();
        add_action('rest_api_init', function () use ($api_endpoints) {
            foreach ($api_endpoints->get_endpoints() as $endpoint) {
                register_rest_route('wcacp/v1', $endpoint['route'], $endpoint);
            }
        });
    }

    /**
     * Get the plugin instance.
     *
     * @return WooCommerce_ACP_Instant_Checkout
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the plugin URL.
     *
     * @return string
     */
    public function get_plugin_url() {
        return WCACP_PLUGIN_URL;
    }

    /**
     * Get the plugin version.
     *
     * @return string
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Get the plugin directory.
     *
     * @return string
     */
    public function get_plugin_dir() {
        return WCACP_PLUGIN_DIR;
    }

    /**
     * Validate ACP request.
     *
     * @param WP_REST_Request $request The request object.
     * @return bool|WP_Error
     */
    public function validate_acp_request($request) {
        // Check if ACP is enabled
        if (get_option('wcacp_enable_acp') !== 'yes') {
            return new WP_Error('acp_disabled', 'ACP is not enabled', array('status' => 403));
        }

        // Validate OpenAI API key if provided in headers
        $api_key = $request->get_header('X-OpenAI-API-Key');
        if ($api_key) {
            $stored_key = get_option('wcacp_openai_api_key');
            if ($api_key !== $stored_key) {
                return new WP_Error('invalid_api_key', 'Invalid API key', array('status' => 401));
            }
        }

        return true;
    }

    /**
     * Log ACP request for debugging.
     *
     * @param string $endpoint The endpoint being called.
     * @param array  $request_data The request data.
     * @param array  $response_data The response data (optional).
     */
    public function log_acp_request($endpoint, $request_data, $response_data = null) {
        if (get_option('wcacp_enable_logging') !== 'yes') {
            return;
        }

        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'endpoint' => $endpoint,
            'request' => $request_data,
            'response' => $response_data,
        );

        error_log('ACP Request: ' . wp_json_encode($log_entry));
    }
}

/**
 * Returns the main instance of WooCommerce_ACP_Instant_Checkout.
 *
 * @return WooCommerce_ACP_Instant_Checkout
 */
function woocommerce_acp_instant_checkout() {
    return WooCommerce_ACP_Instant_Checkout::get_instance();
}

// Initialize the plugin
woocommerce_acp_instant_checkout();