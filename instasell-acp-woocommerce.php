<?php
/**
 * Plugin Name: InstaSell with ACP for WooCommerce
 * Plugin URI:  https://github.com/webvijayi/instasell-acp-woocommerce
 * Description: Enable "Buy it in ChatGPT" using the Agentic Commerce Protocol (ACP). Seamless AI-powered checkout integration helping WooCommerce store owners sell more.
 * Version:     1.0.1
 * Author:      Web Vijayi
 * Author URI:  https://webvijayi.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: instasell-acp-woocommerce
 * Requires Plugins: woocommerce
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Main plugin class
final class INSTSL_Checkout {

    /**
     * Plugin instance.
     *
     * @var INSTSL_Checkout
     */
    private static $instance;

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = '1.0.1';

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
        define('INSTSL_PLUGIN_FILE', __FILE__);
        define('INSTSL_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('INSTSL_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('INSTSL_VERSION', $this->version);
    }

    /**
     * Include required files.
     */
    private function includes() {
        // Load Composer autoloader if available
        if (file_exists(INSTSL_PLUGIN_DIR . 'vendor/autoload.php')) {
            require_once INSTSL_PLUGIN_DIR . 'vendor/autoload.php';
        }

        require_once INSTSL_PLUGIN_DIR . 'includes/class-instsl-admin.php';
        require_once INSTSL_PLUGIN_DIR . 'includes/class-instsl-api-endpoints.php';
        require_once INSTSL_PLUGIN_DIR . 'includes/class-instsl-checkout-session.php';
        require_once INSTSL_PLUGIN_DIR . 'includes/class-instsl-product-feed.php';
        require_once INSTSL_PLUGIN_DIR . 'includes/class-instsl-post-types.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'));
    }

    /**
     * On plugins loaded.
     */
    public function on_plugins_loaded() {
        // Initialization
        new INSTSL_Admin();
        new INSTSL_Product_Feed();

        // Register API endpoints
        $api_endpoints = new INSTSL_API_Endpoints();
        add_action('rest_api_init', function () use ($api_endpoints) {
            foreach ($api_endpoints->get_endpoints() as $endpoint) {
                register_rest_route('instsl/v1', $endpoint['route'], $endpoint);
            }
        });
    }

    /**
     * Get the plugin instance.
     *
     * @return INSTSL_Checkout
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
        return INSTSL_PLUGIN_URL;
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
        return INSTSL_PLUGIN_DIR;
    }

    /**
     * Validate ACP request.
     *
     * @param WP_REST_Request $request The request object.
     * @return bool|WP_Error
     */
    public function validate_acp_request($request) {
        // Check if ACP is enabled
        if (get_option('instsl_enable_acp') !== 'yes') {
            return new WP_Error('acp_disabled', 'ACP is not enabled', array('status' => 403));
        }

        // Validate OpenAI API key if provided in headers
        $api_key = $request->get_header('X-OpenAI-API-Key');
        if ($api_key) {
            $stored_key = get_option('instsl_openai_api_key');
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
        if (get_option('instsl_enable_logging') !== 'yes') {
            return;
        }

        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'endpoint' => $endpoint,
            'request' => $request_data,
            'response' => $response_data,
        );

        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
            error_log('InstaSell ACP Request: ' . wp_json_encode($log_entry));
        }
    }
}

/**
 * Returns the main instance of INSTSL_Checkout.
 *
 * @return INSTSL_Checkout
 */
function instsl_checkout() {
    return INSTSL_Checkout::get_instance();
}

// Initialize the plugin
instsl_checkout();
