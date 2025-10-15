<?php
/**
 * Admin Interface for ACP Settings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WCACP_Admin
 */
class WCACP_Admin {

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
     * Initialize the admin interface
     */
    private function init() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Add settings sections
        add_action('admin_init', array($this, 'register_settings'));

        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Add admin notices
        add_action('admin_notices', array($this, 'admin_notices'));

        // Add meta boxes for ACP orders
        add_action('add_meta_boxes', array($this, 'add_order_meta_boxes'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('ACP Instant Checkout', 'acp-woocommerce-ultraproduction'),
            __('ACP Instant Checkout', 'acp-woocommerce-ultraproduction'),
            'manage_options',
            'wcacp-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'wcacp_settings',
            'wcacp_api_enabled',
            array(
                'type' => 'boolean',
                'default' => 'yes',
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );

        register_setting(
            'wcacp_settings',
            'wcacp_stripe_publishable_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'wcacp_settings',
            'wcacp_stripe_secret_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'wcacp_settings',
            'wcacp_openai_api_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'wcacp_settings',
            'wcacp_webhook_secret',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        // Add settings sections
        add_settings_section(
            'wcacp_api_settings',
            __('API Settings', 'acp-woocommerce-ultraproduction'),
            array($this, 'api_settings_section_callback'),
            'wcacp_settings'
        );

        add_settings_section(
            'wcacp_payment_settings',
            __('Payment Settings', 'acp-woocommerce-ultraproduction'),
            array($this, 'payment_settings_section_callback'),
            'wcacp_settings'
        );

        // Add settings fields
        add_settings_field(
            'wcacp_api_enabled',
            __('Enable ACP API', 'acp-woocommerce-ultraproduction'),
            array($this, 'api_enabled_field_callback'),
            'wcacp_settings',
            'wcacp_api_settings'
        );

        add_settings_field(
            'wcacp_openai_api_key',
            __('OpenAI API Key', 'acp-woocommerce-ultraproduction'),
            array($this, 'openai_api_key_field_callback'),
            'wcacp_settings',
            'wcacp_api_settings'
        );

        add_settings_field(
            'wcacp_stripe_publishable_key',
            __('Stripe Publishable Key', 'acp-woocommerce-ultraproduction'),
            array($this, 'stripe_publishable_key_field_callback'),
            'wcacp_settings',
            'wcacp_payment_settings'
        );

        add_settings_field(
            'wcacp_stripe_secret_key',
            __('Stripe Secret Key', 'acp-woocommerce-ultraproduction'),
            array($this, 'stripe_secret_key_field_callback'),
            'wcacp_settings',
            'wcacp_payment_settings'
        );

        add_settings_field(
            'wcacp_webhook_secret',
            __('Stripe Webhook Secret', 'acp-woocommerce-ultraproduction'),
            array($this, 'webhook_secret_field_callback'),
            'wcacp_settings',
            'wcacp_payment_settings'
        );
    }

    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('WooCommerce ACP Instant Checkout Settings', 'acp-woocommerce-ultraproduction'); ?></h1>

            <p><?php printf(__('Configure your store for OpenAI\'s Agentic Commerce Protocol (ACP) to enable "Buy it in ChatGPT". <a href="%s" target="_blank">Learn more about ACP</a>.', 'acp-woocommerce-ultraproduction'), 'https://openai.com/index/buy-it-in-chatgpt/'); ?></p>

            <form method="post" action="options.php">
                <?php
                settings_fields('wcacp_settings');
                do_settings_sections('wcacp_settings');
                submit_button();
                ?>
            </form>

            <div class="wcacp-admin-info postbox">
                <h2 class="hndle"><span><?php _e('Setup and Configuration', 'acp-woocommerce-ultraproduction'); ?></span></h2>
                <div class="inside">
                    <h3><?php _e('Setup Instructions', 'acp-woocommerce-ultraproduction'); ?></h3>
                    <ol>
                        <li><?php _e('<strong>Enable the ACP API</strong> using the checkbox above.', 'acp-woocommerce-ultraproduction'); ?></li>
                        <li><?php _e('<strong>Enter your Stripe API keys</strong> for secure payment processing.', 'acp-woocommerce-ultraproduction'); ?></li>
                        <li><?php _e('<strong>Submit your Product Feed URL</strong> to OpenAI for indexing.', 'acp-woocommerce-ultraproduction'); ?></li>
                        <li><?php _e('<strong>Test the integration</strong> with OpenAI\'s developer tools or ChatGPT.', 'acp-woocommerce-ultraproduction'); ?></li>
                    </ol>

                    <h3><?php _e('API Endpoints', 'acp-woocommerce-ultraproduction'); ?></h3>
                    <p><strong><?php _e('Product Feed URL:', 'acp-woocommerce-ultraproduction'); ?></strong></p>
                    <p><code><?php echo esc_url(rest_url('wcacp/v1/product-feed')); ?></code></p>
                    <p class="description"><?php _e('This URL provides a live feed of your products to ACP-compatible agents.', 'acp-woocommerce-ultraproduction'); ?></p>

                    <h3><?php _e('Stripe Webhook Configuration', 'acp-woocommerce-ultraproduction'); ?></h3>
                    <p><strong><?php _e('Webhook URL:', 'acp-woocommerce-ultraproduction'); ?></strong></p>
                    <p><code><?php echo esc_url(rest_url('wcacp/v1/webhooks/stripe')); ?></code></p>
                    <p class="description"><?php _e('Add this URL in your Stripe dashboard to receive payment notifications. Recommended events: <code>payment_intent.succeeded</code>, <code>payment_intent.payment_failed</code>.', 'acp-woocommerce-ultraproduction'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * API settings section callback
     */
    public function api_settings_section_callback() {
        echo '<p>' . __('Enable or disable the ACP API and enter your OpenAI API key.', 'acp-woocommerce-ultraproduction') . '</p>';
    }

    /**
     * Payment settings section callback
     */
    public function payment_settings_section_callback() {
        echo '<p>' . __('Enter your Stripe API keys and webhook secret to enable secure payments.', 'acp-woocommerce-ultraproduction') . '</p>';
    }

    /**
     * API enabled field callback
     */
    public function api_enabled_field_callback() {
        $value = get_option('wcacp_api_enabled', 'yes');
        ?>
        <input type="checkbox" name="wcacp_api_enabled" value="1" <?php checked($value, '1'); ?> />
        <label for="wcacp_api_enabled"><?php _e('Enable ACP API endpoints', 'acp-woocommerce-ultraproduction'); ?></label>
        <?php
    }

    /**
     * OpenAI API key field callback
     */
    public function openai_api_key_field_callback() {
        $value = get_option('wcacp_openai_api_key', '');
        ?>
        <input type="password" name="wcacp_openai_api_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php _e('API key for authenticating ACP requests from OpenAI.', 'acp-woocommerce-ultraproduction'); ?></p>
        <?php
    }

    /**
     * Stripe publishable key field callback
     */
    public function stripe_publishable_key_field_callback() {
        $value = get_option('wcacp_stripe_publishable_key', '');
        ?>
        <input type="text" name="wcacp_stripe_publishable_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php _e('Your Stripe publishable key.', 'acp-woocommerce-ultraproduction'); ?></p>
        <?php
    }

    /**
     * Stripe secret key field callback
     */
    public function stripe_secret_key_field_callback() {
        $value = get_option('wcacp_stripe_secret_key', '');
        ?>
        <input type="password" name="wcacp_stripe_secret_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php _e('Your Stripe secret key.', 'acp-woocommerce-ultraproduction'); ?></p>
        <?php
    }

    /**
     * Webhook secret field callback
     */
    public function webhook_secret_field_callback() {
        $value = get_option('wcacp_webhook_secret', '');
        ?>
        <input type="password" name="wcacp_webhook_secret" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php _e('Stripe webhook secret for verifying webhook signatures.', 'acp-woocommerce-ultraproduction'); ?></p>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_wcacp-settings') {
            return;
        }

        wp_enqueue_style(
            'wcacp-admin',
            $this->plugin->get_plugin_url() . 'assets/css/admin.css',
            array(),
            $this->plugin->get_version()
        );

        wp_enqueue_script(
            'wcacp-admin',
            $this->plugin->get_plugin_url() . 'assets/js/admin.js',
            array('jquery'),
            $this->plugin->get_version(),
            true
        );
    }

    /**
     * Admin notices
     */
    public function admin_notices() {
        // Check if API is enabled but keys are missing
        if (get_option('wcacp_api_enabled', 'no') === 'yes') {
            if (empty(get_option('wcacp_openai_api_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php printf(__('ACP Instant Checkout: An OpenAI API key is required for ACP functionality. Please <a href="%s">enter your API key</a>.', 'acp-woocommerce-ultraproduction'), esc_url(admin_url('options-general.php?page=wcacp-settings'))); ?></p>
                </div>
                <?php
            }

            if (empty(get_option('wcacp_stripe_secret_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php printf(__('ACP Instant Checkout: A Stripe secret key is required for payment processing. Please <a href="%s">enter your Stripe keys</a>.', 'acp-woocommerce-ultraproduction'), esc_url(admin_url('options-general.php?page=wcacp-settings'))); ?></p>
                </div>
                <?php
            }
        }

        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            ?>
            <div class="notice notice-error">
                <p><?php _e('ACP Instant Checkout requires WooCommerce to be installed and active.', 'acp-woocommerce-ultraproduction'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Add order meta boxes
     */
    public function add_order_meta_boxes() {
        add_meta_box(
            'wcacp-order-info',
            __('ACP Information', 'acp-woocommerce-ultraproduction'),
            array($this, 'order_meta_box_callback'),
            'shop_order',
            'side',
            'default'
        );
    }

    /**
     * Order meta box callback
     */
    public function order_meta_box_callback($post) {
        $acp_session_id = get_post_meta($post->ID, '_acp_session_id', true);
        $acp_payment_intent_id = get_post_meta($post->ID, '_acp_payment_intent_id', true);

        if (!$acp_session_id) {
            echo '<p>' . __('This order was not created through ACP.', 'acp-woocommerce-ultraproduction') . '</p>';
            return;
        }

        ?>
        <p><strong><?php _e('ACP Session ID:', 'acp-woocommerce-ultraproduction'); ?></strong></p>
        <p><code><?php echo esc_html($acp_session_id); ?></code></p>

        <?php if ($acp_payment_intent_id): ?>
        <p><strong><?php _e('Payment Intent ID:', 'acp-woocommerce-ultraproduction'); ?></strong></p>
        <p><code><?php echo esc_html($acp_payment_intent_id); ?></code></p>
        <?php endif; ?>

        <p><em><?php _e('This order was created through OpenAI\'s "Buy it in ChatGPT" feature.', 'acp-woocommerce-ultraproduction'); ?></em></p>
        <?php
    }
}
