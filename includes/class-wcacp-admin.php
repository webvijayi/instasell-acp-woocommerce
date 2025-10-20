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

        // Add AJAX action for testing connections
        add_action('wp_ajax_wcacp_test_connection', array($this, 'test_connection'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('ACP Instant Checkout', 'woocommerce-acp-instant-checkout'),
            __('ACP Instant Checkout', 'woocommerce-acp-instant-checkout'),
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
            __('API Settings', 'woocommerce-acp-instant-checkout'),
            array($this, 'api_settings_section_callback'),
            'wcacp_settings'
        );

        add_settings_section(
            'wcacp_payment_settings',
            __('Payment Settings', 'woocommerce-acp-instant-checkout'),
            array($this, 'payment_settings_section_callback'),
            'wcacp_settings'
        );

        // Add settings fields
        add_settings_field(
            'wcacp_api_enabled',
            __('Enable ACP API', 'woocommerce-acp-instant-checkout'),
            array($this, 'api_enabled_field_callback'),
            'wcacp_settings',
            'wcacp_api_settings'
        );

        add_settings_field(
            'wcacp_openai_api_key',
            __('OpenAI API Key', 'woocommerce-acp-instant-checkout'),
            array($this, 'openai_api_key_field_callback'),
            'wcacp_settings',
            'wcacp_api_settings'
        );

        add_settings_field(
            'wcacp_stripe_publishable_key',
            __('Stripe Publishable Key', 'woocommerce-acp-instant-checkout'),
            array($this, 'stripe_publishable_key_field_callback'),
            'wcacp_settings',
            'wcacp_payment_settings'
        );

        add_settings_field(
            'wcacp_stripe_secret_key',
            __('Stripe Secret Key', 'woocommerce-acp-instant-checkout'),
            array($this, 'stripe_secret_key_field_callback'),
            'wcacp_settings',
            'wcacp_payment_settings'
        );

        add_settings_field(
            'wcacp_webhook_secret',
            __('Stripe Webhook Secret', 'woocommerce-acp-instant-checkout'),
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
            <h1><?php esc_html_e('ChatGPT Instant Checkout - ACP Settings', 'woocommerce-acp-instant-checkout'); ?></h1>

            <p>
                <?php
                /* translators: %s: URL to OpenAI ACP documentation */
                printf(esc_html__('Configure your store for OpenAI\'s Agentic Commerce Protocol (ACP) to enable "Buy it in ChatGPT". <a href="%s" target="_blank">Learn more about ACP</a>.', 'woocommerce-acp-instant-checkout'), 'https://openai.com/index/buy-it-in-chatgpt/');
                ?>
            </p>

            <form method="post" action="options.php">
                <?php
                settings_fields('wcacp_settings');
                do_settings_sections('wcacp_settings');
                submit_button();
                ?>
            </form>

            <div class="wcacp-admin-info postbox">
                <h2 class="hndle"><span><?php esc_html_e('Setup and Configuration', 'woocommerce-acp-instant-checkout'); ?></span></h2>
                <div class="inside">
                    <h3><?php esc_html_e('Setup Instructions', 'woocommerce-acp-instant-checkout'); ?></h3>
                    <ol>
                        <li>
                            <?php
                            /* translators: %s: URL to OpenAI ACP application page */
                            printf( esc_html__( 'Apply for OpenAI ACP approval at %s.', 'woocommerce-acp-instant-checkout' ), '<a href="https://openai.com/index/buy-it-in-chatgpt/" target="_blank">https://openai.com/index/buy-it-in-chatgpt/</a>' );
                            ?>
                        </li>
                        <li><?php esc_html_e('(After approval) Copy your ACP OpenAI API Key from the OpenAI ACP dashboard.', 'woocommerce-acp-instant-checkout'); ?></li>
                        <li><?php esc_html_e('Enable the ACP API and paste your OpenAI API Key in the settings above.', 'woocommerce-acp-instant-checkout'); ?></li>
                        <li><?php esc_html_e('Enter your Stripe API keys for payment processing.', 'woocommerce-acp-instant-checkout'); ?></li>
                        <li><?php esc_html_e('Submit your Product Feed URL to OpenAI for indexing if required or requested.', 'woocommerce-acp-instant-checkout'); ?></li>
                        <li><?php esc_html_e('Test and complete your integration using OpenAI\'s developer tools or ChatGPT.', 'woocommerce-acp-instant-checkout'); ?></li>
                    </ol>

                    <h3><?php esc_html_e('API Endpoints', 'woocommerce-acp-instant-checkout'); ?></h3>
                    <p><strong><?php esc_html_e('Product Feed URL:', 'woocommerce-acp-instant-checkout'); ?></strong></p>
                    <p><code><?php echo esc_url(rest_url('wcacp/v1/product-feed')); ?></code> <button type="button" class="button button-small wcacp-copy-to-clipboard" data-target="wcacp-product-feed-url"><?php esc_html_e('Copy', 'woocommerce-acp-instant-checkout'); ?></button></p>
                    <p class="description"><?php esc_html_e('This URL provides a live feed of your products to ACP-compatible agents.', 'woocommerce-acp-instant-checkout'); ?></p>

                    <h3><?php esc_html_e('Stripe Webhook Configuration', 'woocommerce-acp-instant-checkout'); ?></h3>
                    <p><strong><?php esc_html_e('Webhook URL:', 'woocommerce-acp-instant-checkout'); ?></strong></p>
                    <p><code><?php echo esc_url(rest_url('wcacp/v1/webhooks/stripe')); ?></code> <button type="button" class="button button-small wcacp-copy-to-clipboard" data-target="wcacp-webhook-url"><?php esc_html_e('Copy', 'woocommerce-acp-instant-checkout'); ?></button></p>
                    <p class="description"><?php esc_html_e('Add this URL in your Stripe dashboard to receive payment notifications. Recommended events: <code>payment_intent.succeeded</code>, <code>payment_intent.payment_failed</code>.', 'woocommerce-acp-instant-checkout'); ?></p>

                    <h3><?php esc_html_e('Advanced Options', 'woocommerce-acp-instant-checkout'); ?></h3>
                    <p><?php esc_html_e('For advanced users, you can enable debug logging, set webhook event types, or configure the product count in the feed.', 'woocommerce-acp-instant-checkout'); ?></p>

                    <h3><?php esc_html_e('Help and Documentation', 'woocommerce-acp-instant-checkout'); ?></h3>
                    <p>
                        <?php
                        /* translators: %s: URL to plugin documentation */
                        printf(esc_html__('For more information, please visit the <a href="%s" target="_blank">plugin documentation</a>.', 'woocommerce-acp-instant-checkout'), 'https://openai.com/index/buy-it-in-chatgpt/');
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * API settings section callback
     */
    public function api_settings_section_callback() {
        echo '<p>' . esc_html__('Enable or disable the ACP API and enter your OpenAI API key.', 'woocommerce-acp-instant-checkout') . '</p>';
    }

    /**
     * Payment settings section callback
     */
    public function payment_settings_section_callback() {
        echo '<p>' . esc_html__('Enter your Stripe API keys and webhook secret to enable secure payments.', 'woocommerce-acp-instant-checkout') . '</p>';
    }

    /**
     * API enabled field callback
     */
    public function api_enabled_field_callback() {
        $value = get_option('wcacp_api_enabled', 'yes');
        ?>
        <input type="checkbox" name="wcacp_api_enabled" value="1" <?php checked($value, '1'); ?> />
        <label for="wcacp_api_enabled"><?php esc_html_e('Enable ACP API endpoints', 'woocommerce-acp-instant-checkout'); ?></label>
        <?php
    }

    /**
     * OpenAI API key field callback
     */
    public function openai_api_key_field_callback() {
        $value = get_option('wcacp_openai_api_key', '');
        ?>
        <input type="password" name="wcacp_openai_api_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('API key for authenticating ACP requests from OpenAI (received after OpenAI approval).', 'woocommerce-acp-instant-checkout'); ?></p>
        <button type="button" class="button button-secondary wcacp-test-connection" data-type="openai"><?php esc_html_e('Test API', 'woocommerce-acp-instant-checkout'); ?></button>
        <?php
    }

    /**
     * Stripe publishable key field callback
     */
    public function stripe_publishable_key_field_callback() {
        $value = get_option('wcacp_stripe_publishable_key', '');
        ?>
        <input type="text" name="wcacp_stripe_publishable_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Your Stripe publishable key.', 'woocommerce-acp-instant-checkout'); ?></p>
        <button type="button" class="button button-secondary wcacp-test-connection" data-type="stripe"><?php esc_html_e('Test Connection', 'woocommerce-acp-instant-checkout'); ?></button>
        <?php
    }

    /**
     * Stripe secret key field callback
     */
    public function stripe_secret_key_field_callback() {
        $value = get_option('wcacp_stripe_secret_key', '');
        ?>
        <input type="password" name="wcacp_stripe_secret_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Your Stripe secret key.', 'woocommerce-acp-instant-checkout'); ?></p>
        <?php
    }

    /**
     * Webhook secret field callback
     */
    public function webhook_secret_field_callback() {
        $value = get_option('wcacp_webhook_secret', '');
        ?>
        <input type="password" name="wcacp_webhook_secret" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Stripe webhook secret for verifying webhook signatures.', 'woocommerce-acp-instant-checkout'); ?></p>
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
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('ACP Instant Checkout: An OpenAI API key is required for ACP functionality. Please <a href="%s">enter your API key</a>.', 'woocommerce-acp-instant-checkout'), esc_url(admin_url('options-general.php?page=wcacp-settings')));
                        ?>
                    </p>
                </div>
                <?php
            }

            if (empty(get_option('wcacp_stripe_secret_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('ACP Instant Checkout: A Stripe secret key is required for payment processing. Please <a href="%s">enter your Stripe keys</a>.', 'woocommerce-acp-instant-checkout'), esc_url(admin_url('options-general.php?page=wcacp-settings')));
                        ?>
                    </p>
                </div>
                <?php
            }
        }

        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            ?>
            <div class="notice notice-error">
                <p><?php esc_html_e('ACP Instant Checkout requires WooCommerce to be installed and active.', 'woocommerce-acp-instant-checkout'); ?></p>
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
            __('ACP Information', 'woocommerce-acp-instant-checkout'),
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
            echo '<p>' . esc_html__('This order was not created through ACP.', 'woocommerce-acp-instant-checkout') . '</p>';
            return;
        }

        ?>
        <p><strong><?php esc_html_e('ACP Session ID:', 'woocommerce-acp-instant-checkout'); ?></strong></p>
        <p><code><?php echo esc_html($acp_session_id); ?></code></p>

        <?php if ($acp_payment_intent_id): ?>
        <p><strong><?php esc_html_e('Payment Intent ID:', 'woocommerce-acp-instant-checkout'); ?></strong></p>
        <p><code><?php echo esc_html($acp_payment_intent_id); ?></code></p>
        <?php endif; ?>

        <p><em><?php esc_html_e('This order was created through OpenAI\'s "Buy it in ChatGPT" feature.', 'woocommerce-acp-instant-checkout'); ?></em></p>
        <?php
    }

    /**
     * Test API connection
     */
    public function test_connection() {
        check_ajax_referer('wcacp_admin_nonce', 'nonce');

        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';

        if ($type === 'openai') {
            $api_key = get_option('wcacp_openai_api_key');
            if (empty($api_key)) {
                wp_send_json_error('OpenAI API key is not set.');
            }

            // TODO: Implement OpenAI API connection test
            wp_send_json_success();
        } elseif ($type === 'stripe') {
            $secret_key = get_option('wcacp_stripe_secret_key');
            if (empty($secret_key)) {
                wp_send_json_error('Stripe secret key is not set.');
            }

            // TODO: Implement Stripe API connection test
            wp_send_json_success();
        }

        wp_send_json_error('Invalid API type.');
    }
}

