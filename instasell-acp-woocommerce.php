<?php
/**
 * Plugin Name: InstaSell with ACP for WooCommerce
 * Plugin URI:  https://github.com/webvijayi/instasell-acp-woocommerce
 * Description: Enable "Buy it in ChatGPT" using the Agentic Commerce Protocol (ACP). Seamless AI-powered checkout integration helping WooCommerce store owners sell more.
 * Version:     1.0.2
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

/**
 * Check if WooCommerce is active and show admin notice if not.
 */
function instsl_check_woocommerce_dependency() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'instsl_woocommerce_missing_notice');
        return false;
    }
    return true;
}

/**
 * Display admin notice when WooCommerce is not active.
 */
function instsl_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <strong><?php esc_html_e('InstaSell with ACP for WooCommerce', 'instasell-acp-woocommerce'); ?></strong>
            <?php esc_html_e('requires WooCommerce to be installed and active.', 'instasell-acp-woocommerce'); ?>
        </p>
        <p>
            <?php
            /* translators: %s: WooCommerce plugin URL */
            echo wp_kses_post(
                sprintf(
                    __('Please install and activate <a href="%s" target="_blank">WooCommerce</a> to use this plugin.', 'instasell-acp-woocommerce'),
                    'https://wordpress.org/plugins/woocommerce/'
                )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Deactivate plugin if WooCommerce is not active.
 */
function instsl_deactivate_on_woocommerce_missing() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));

        // Prevent "Plugin activated" notice
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        add_action('admin_notices', 'instsl_woocommerce_deactivation_notice');
    }
}

/**
 * Display notice when plugin is deactivated due to missing WooCommerce.
 */
function instsl_woocommerce_deactivation_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <strong><?php esc_html_e('InstaSell with ACP for WooCommerce', 'instasell-acp-woocommerce'); ?></strong>
            <?php esc_html_e('has been deactivated because WooCommerce is not installed or active.', 'instasell-acp-woocommerce'); ?>
        </p>
        <p>
            <?php
            /* translators: %s: WooCommerce plugin URL */
            echo wp_kses_post(
                sprintf(
                    __('Please install and activate <a href="%s" target="_blank">WooCommerce</a> first, then activate InstaSell.', 'instasell-acp-woocommerce'),
                    'https://wordpress.org/plugins/woocommerce/'
                )
            );
            ?>
        </p>
    </div>
    <?php
}

// Register activation hook to check for WooCommerce
register_activation_hook(__FILE__, 'instsl_deactivate_on_woocommerce_missing');

// Check if WooCommerce gets deactivated
add_action('admin_init', function() {
    // Only check on plugin admin pages
    if (is_admin() && current_user_can('activate_plugins')) {
        instsl_check_woocommerce_dependency();
    }
});

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
    public $version = '1.0.2';

    /**
     * Constructor.
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init();
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
     * Initialize plugin components.
     */
    private function init() {
        // Initialize admin interface
        new INSTSL_Admin();

        // Initialize product feed
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
 * @return INSTSL_Checkout|null
 */
function instsl_checkout() {
    // Only initialize if WooCommerce is active
    if (!instsl_check_woocommerce_dependency()) {
        return null;
    }
    return INSTSL_Checkout::get_instance();
}

// Initialize the plugin (only if WooCommerce is active)
add_action('plugins_loaded', 'instsl_checkout', 20);
