<?php
/**
 * Plugin Name: Instant Checkout via ACP Agentic Commerce for WooCommerce
 * Plugin URI:  https://github.com/webvijayi/instant-checkout-via-acp-agentic-commerce-for-woocommerce
 * Description: Enable "Buy it in ChatGPT" using the Agentic Commerce Protocol (ACP). Seamless AI-powered instant checkout integration for WooCommerce stores.
 * Version:     1.1.0
 * Author:      Web Vijayi
 * Author URI:  https://webvijayi.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: instant-checkout-via-acp-agentic-commerce-for-woocommerce
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
function icvaac_check_woocommerce_dependency() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'icvaac_woocommerce_missing_notice');
        return false;
    }
    return true;
}

/**
 * Display admin notice when WooCommerce is not active.
 */
function icvaac_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <strong><?php esc_html_e('Instant Checkout via ACP Agentic Commerce for WooCommerce', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
            <?php esc_html_e('requires WooCommerce to be installed and active.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
        </p>
        <p>
            <?php
            echo wp_kses_post(
                sprintf(
                    /* translators: %s: WooCommerce plugin URL */
                    __('Please install and activate <a href="%s" target="_blank">WooCommerce</a> to use this plugin.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
                    'https://wordpress.org/plugins/woocommerce/'
                )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Check for WooCommerce on plugin activation.
 * Prevents activation if WooCommerce is not active.
 */
function icvaac_activation_check() {
    if (!class_exists('WooCommerce')) {
        /* translators: %s: WooCommerce plugin URL */
        $message = esc_html__('Instant Checkout via ACP Agentic Commerce for WooCommerce requires WooCommerce to be installed and active. Please install and activate WooCommerce first, then try activating this plugin again.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce');
        $message .= ' <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">' . esc_html__('Download WooCommerce', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce') . '</a>';

        wp_die(
            wp_kses_post($message),
            esc_html__('Plugin Activation Error', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array('back_link' => true)
        );
    }
}

// Register activation hook to check for WooCommerce
register_activation_hook(__FILE__, 'icvaac_activation_check');

// Main plugin class
final class ICVAAC_Checkout {

    /**
     * Plugin instance.
     *
     * @var ICVAAC_Checkout
     */
    private static $instance;

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = '1.1.0';

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
        define('ICVAAC_PLUGIN_FILE', __FILE__);
        define('ICVAAC_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('ICVAAC_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('ICVAAC_VERSION', $this->version);
    }

    /**
     * Include required files.
     */
    private function includes() {
        // Load Composer autoloader if available
        if (file_exists(ICVAAC_PLUGIN_DIR . 'vendor/autoload.php')) {
            require_once ICVAAC_PLUGIN_DIR . 'vendor/autoload.php';
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC: Composer autoloader loaded');
            }
        } else {
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC: Composer autoloader not found at ' . ICVAAC_PLUGIN_DIR . 'vendor/autoload.php');
            }
        }

        $required_files = array(
            'includes/class-icvaac-admin.php',
            'includes/class-icvaac-api-endpoints.php',
            'includes/class-icvaac-checkout-session.php',
            'includes/class-icvaac-product-feed.php',
            'includes/class-icvaac-post-types.php',
        );

        foreach ($required_files as $file) {
            $file_path = ICVAAC_PLUGIN_DIR . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
                if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                    error_log('ICVAAC: Loaded ' . $file);
                }
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                    error_log('ICVAAC ERROR: Required file not found: ' . $file_path);
                }
                wp_die(
                    esc_html(sprintf('ICVAAC: Required file not found: %s', $file)),
                    esc_html__('Plugin Error', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce')
                );
            }
        }
    }

    /**
     * Initialize plugin components.
     */
    private function init() {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
            error_log('ICVAAC: Starting init()');
        }

        try {
            // Initialize admin interface
            new ICVAAC_Admin();
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC: ICVAAC_Admin initialized');
            }

            // Initialize product feed
            new ICVAAC_Product_Feed();
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC: ICVAAC_Product_Feed initialized');
            }

            // Register API endpoints
            $api_endpoints = new ICVAAC_API_Endpoints();
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC: ICVAAC_API_Endpoints initialized');
            }

            add_action('rest_api_init', function () use ($api_endpoints) {
                foreach ($api_endpoints->get_endpoints() as $endpoint) {
                    register_rest_route('icvaac/v1', $endpoint['route'], $endpoint);
                }
            });

            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC: init() completed successfully');
            }
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC FATAL ERROR in init(): ' . $e->getMessage());
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
                error_log('ICVAAC Stack trace: ' . $e->getTraceAsString());
            }
            throw $e;
        }
    }

    /**
     * Get the plugin instance.
     *
     * @return ICVAAC_Checkout
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
        return ICVAAC_PLUGIN_URL;
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
        return ICVAAC_PLUGIN_DIR;
    }

    /**
     * Validate ACP request.
     *
     * @param WP_REST_Request $request The request object.
     * @return bool|WP_Error
     */
    public function validate_acp_request($request) {
        // Check if ACP is enabled
        if (get_option('icvaac_api_enabled') !== '1') {
            return new WP_Error('acp_disabled', 'ACP is not enabled', array('status' => 403));
        }

        // Validate OpenAI API key if provided in headers
        $api_key = $request->get_header('X-OpenAI-API-Key');
        if ($api_key) {
            $stored_key = get_option('icvaac_openai_api_key');
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
        if (get_option('icvaac_enable_logging') !== 'yes') {
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
            error_log('ICVAAC ACP Request: ' . wp_json_encode($log_entry));
        }
    }
}

/**
 * Returns the main instance of ICVAAC_Checkout.
 *
 * @return ICVAAC_Checkout|null
 */
function icvaac_checkout() {
    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
        error_log('ICVAAC: icvaac_checkout() called');
    }

    // Only initialize if WooCommerce is active
    if (!icvaac_check_woocommerce_dependency()) {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
            error_log('ICVAAC: WooCommerce dependency check failed');
        }
        return null;
    }

    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
        error_log('ICVAAC: Getting ICVAAC_Checkout instance');
    }

    $instance = ICVAAC_Checkout::get_instance();

    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG_LOG is enabled
        error_log('ICVAAC: Instance created successfully');
    }

    return $instance;
}

// Initialize the plugin (only if WooCommerce is active)
add_action('plugins_loaded', 'icvaac_checkout', 20);
