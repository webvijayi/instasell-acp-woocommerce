<?php
/**
 * Admin Interface for ACP Settings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class INSTSL_Admin
 */
class INSTSL_Admin {

    /**
     * Plugin instance
     */
    private $plugin;

    /**
     * Constructor
     */
    public function __construct() {
        $this->plugin = instsl_checkout();
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
        add_action('wp_ajax_instsl_test_connection', array($this, 'test_connection'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('InstaSell ACP Checkout', 'instasell-acp-woocommerce'),
            __('InstaSell ACP Checkout', 'instasell-acp-woocommerce'),
            'manage_options',
            'instsl-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'instsl_settings',
            'instsl_api_enabled',
            array(
                'type' => 'boolean',
                'default' => 'yes',
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );

        register_setting(
            'instsl_settings',
            'instsl_stripe_publishable_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'instsl_settings',
            'instsl_stripe_secret_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'instsl_settings',
            'instsl_openai_api_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'instsl_settings',
            'instsl_webhook_secret',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        // Add settings sections
        add_settings_section(
            'instsl_api_settings',
            __('API Settings', 'instasell-acp-woocommerce'),
            array($this, 'api_settings_section_callback'),
            'instsl_settings'
        );

        add_settings_section(
            'instsl_payment_settings',
            __('Payment Settings', 'instasell-acp-woocommerce'),
            array($this, 'payment_settings_section_callback'),
            'instsl_settings'
        );

        // Add settings fields
        add_settings_field(
            'instsl_api_enabled',
            __('Enable ACP API', 'instasell-acp-woocommerce'),
            array($this, 'api_enabled_field_callback'),
            'instsl_settings',
            'instsl_api_settings'
        );

        add_settings_field(
            'instsl_openai_api_key',
            __('OpenAI API Key', 'instasell-acp-woocommerce'),
            array($this, 'openai_api_key_field_callback'),
            'instsl_settings',
            'instsl_api_settings'
        );

        add_settings_field(
            'instsl_stripe_publishable_key',
            __('Stripe Publishable Key', 'instasell-acp-woocommerce'),
            array($this, 'stripe_publishable_key_field_callback'),
            'instsl_settings',
            'instsl_payment_settings'
        );

        add_settings_field(
            'instsl_stripe_secret_key',
            __('Stripe Secret Key', 'instasell-acp-woocommerce'),
            array($this, 'stripe_secret_key_field_callback'),
            'instsl_settings',
            'instsl_payment_settings'
        );

        add_settings_field(
            'instsl_webhook_secret',
            __('Stripe Webhook Secret', 'instasell-acp-woocommerce'),
            array($this, 'webhook_secret_field_callback'),
            'instsl_settings',
            'instsl_payment_settings'
        );
    }

    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('InstaSell ACP Checkout - Settings', 'instasell-acp-woocommerce'); ?></h1>

            <p>
                <?php
                /* translators: %s: URL to OpenAI ACP documentation */
                printf(esc_html__('Configure your store for OpenAI\'s Agentic Commerce Protocol (ACP) to enable "Buy it in ChatGPT". <a href="%s" target="_blank">Learn more about ACP</a>.', 'instasell-acp-woocommerce'), 'https://openai.com/index/buy-it-in-chatgpt/');
                ?>
            </p>

            <form method="post" action="options.php">
                <?php
                settings_fields('instsl_settings');
                do_settings_sections('instsl_settings');
                submit_button();
                ?>
            </form>

            <div class="instsl-admin-info postbox">
                <h2 class="hndle"><span><?php esc_html_e('Setup and Configuration', 'instasell-acp-woocommerce'); ?></span></h2>
                <div class="inside">
                    <h3><?php esc_html_e('Setup Instructions', 'instasell-acp-woocommerce'); ?></h3>
                    <ol>
                        <li>
                            <?php
                            /* translators: %s: URL to OpenAI ACP application page */
                            printf( esc_html__( 'Apply for OpenAI ACP approval at %s.', 'instasell-acp-woocommerce' ), '<a href="https://openai.com/index/buy-it-in-chatgpt/" target="_blank">https://openai.com/index/buy-it-in-chatgpt/</a>' );
                            ?>
                        </li>
                        <li><?php esc_html_e('(After approval) Copy your ACP OpenAI API Key from the OpenAI ACP dashboard.', 'instasell-acp-woocommerce'); ?></li>
                        <li><?php esc_html_e('Enable the ACP API and paste your OpenAI API Key in the settings above.', 'instasell-acp-woocommerce'); ?></li>
                        <li><?php esc_html_e('Enter your Stripe API keys for payment processing.', 'instasell-acp-woocommerce'); ?></li>
                        <li><?php esc_html_e('Submit your Product Feed URL to OpenAI for indexing if required or requested.', 'instasell-acp-woocommerce'); ?></li>
                        <li><?php esc_html_e('Test and complete your integration using OpenAI\'s developer tools or ChatGPT.', 'instasell-acp-woocommerce'); ?></li>
                    </ol>

                    <h3><?php esc_html_e('API Endpoints', 'instasell-acp-woocommerce'); ?></h3>
                    <p><strong><?php esc_html_e('Product Feed URL:', 'instasell-acp-woocommerce'); ?></strong></p>
                    <p><code><?php echo esc_url(rest_url('instsl/v1/product-feed')); ?></code> <button type="button" class="button button-small instsl-copy-to-clipboard" data-target="instsl-product-feed-url"><?php esc_html_e('Copy', 'instasell-acp-woocommerce'); ?></button></p>
                    <p class="description"><?php esc_html_e('This URL provides a live feed of your products to ACP-compatible agents.', 'instasell-acp-woocommerce'); ?></p>

                    <h3><?php esc_html_e('Stripe Webhook Configuration', 'instasell-acp-woocommerce'); ?></h3>
                    <p><strong><?php esc_html_e('Webhook URL:', 'instasell-acp-woocommerce'); ?></strong></p>
                    <p><code><?php echo esc_url(rest_url('instsl/v1/webhooks/stripe')); ?></code> <button type="button" class="button button-small instsl-copy-to-clipboard" data-target="instsl-webhook-url"><?php esc_html_e('Copy', 'instasell-acp-woocommerce'); ?></button></p>
                    <p class="description"><?php esc_html_e('Add this URL in your Stripe dashboard to receive payment notifications. Recommended events: <code>payment_intent.succeeded</code>, <code>payment_intent.payment_failed</code>.', 'instasell-acp-woocommerce'); ?></p>

                    <h3><?php esc_html_e('Advanced Options', 'instasell-acp-woocommerce'); ?></h3>
                    <p><?php esc_html_e('For advanced users, you can enable debug logging, set webhook event types, or configure the product count in the feed.', 'instasell-acp-woocommerce'); ?></p>

                    <h3><?php esc_html_e('Help and Documentation', 'instasell-acp-woocommerce'); ?></h3>
                    <p>
                        <?php
                        /* translators: %s: URL to plugin documentation */
                        printf(esc_html__('For more information, please visit the <a href="%s" target="_blank">plugin documentation</a>.', 'instasell-acp-woocommerce'), 'https://openai.com/index/buy-it-in-chatgpt/');
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
        echo '<p>' . esc_html__('Enable or disable the ACP API and enter your OpenAI API key.', 'instasell-acp-woocommerce') . '</p>';
    }

    /**
     * Payment settings section callback
     */
    public function payment_settings_section_callback() {
        echo '<p>' . esc_html__('Enter your Stripe API keys and webhook secret to enable secure payments.', 'instasell-acp-woocommerce') . '</p>';
    }

    /**
     * API enabled field callback
     */
    public function api_enabled_field_callback() {
        $value = get_option('instsl_api_enabled', 'yes');
        ?>
        <input type="checkbox" name="instsl_api_enabled" value="1" <?php checked($value, '1'); ?> />
        <label for="instsl_api_enabled"><?php esc_html_e('Enable ACP API endpoints', 'instasell-acp-woocommerce'); ?></label>
        <?php
    }

    /**
     * OpenAI API key field callback
     */
    public function openai_api_key_field_callback() {
        $value = get_option('instsl_openai_api_key', '');
        ?>
        <input type="password" name="instsl_openai_api_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('API key for authenticating ACP requests from OpenAI (received after OpenAI approval).', 'instasell-acp-woocommerce'); ?></p>
        <button type="button" class="button button-secondary instsl-test-connection" data-type="openai"><?php esc_html_e('Test API', 'instasell-acp-woocommerce'); ?></button>
        <?php
    }

    /**
     * Stripe publishable key field callback
     */
    public function stripe_publishable_key_field_callback() {
        $value = get_option('instsl_stripe_publishable_key', '');
        ?>
        <input type="text" name="instsl_stripe_publishable_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Your Stripe publishable key.', 'instasell-acp-woocommerce'); ?></p>
        <button type="button" class="button button-secondary instsl-test-connection" data-type="stripe"><?php esc_html_e('Test Connection', 'instasell-acp-woocommerce'); ?></button>
        <?php
    }

    /**
     * Stripe secret key field callback
     */
    public function stripe_secret_key_field_callback() {
        $value = get_option('instsl_stripe_secret_key', '');
        ?>
        <input type="password" name="instsl_stripe_secret_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Your Stripe secret key.', 'instasell-acp-woocommerce'); ?></p>
        <?php
    }

    /**
     * Webhook secret field callback
     */
    public function webhook_secret_field_callback() {
        $value = get_option('instsl_webhook_secret', '');
        ?>
        <input type="password" name="instsl_webhook_secret" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Stripe webhook secret for verifying webhook signatures.', 'instasell-acp-woocommerce'); ?></p>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_instsl-settings') {
            return;
        }

        wp_enqueue_style(
            'instsl-admin',
            $this->plugin->get_plugin_url() . 'assets/css/admin.css',
            array(),
            $this->plugin->get_version()
        );

        wp_enqueue_script(
            'instsl-admin',
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
        if (get_option('instsl_api_enabled', 'no') === 'yes') {
            if (empty(get_option('instsl_openai_api_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('InstaSell ACP Checkout: An OpenAI API key is required for ACP functionality. Please <a href="%s">enter your API key</a>.', 'instasell-acp-woocommerce'), esc_url(admin_url('options-general.php?page=instsl-settings')));
                        ?>
                    </p>
                </div>
                <?php
            }

            if (empty(get_option('instsl_stripe_secret_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('InstaSell ACP Checkout: A Stripe secret key is required for payment processing. Please <a href="%s">enter your Stripe keys</a>.', 'instasell-acp-woocommerce'), esc_url(admin_url('options-general.php?page=instsl-settings')));
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
                <p><?php esc_html_e('InstaSell ACP Checkout requires WooCommerce to be installed and active.', 'instasell-acp-woocommerce'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Add order meta boxes
     */
    public function add_order_meta_boxes() {
        add_meta_box(
            'instsl-order-info',
            __('ACP Information', 'instasell-acp-woocommerce'),
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
            echo '<p>' . esc_html__('This order was not created through ACP.', 'instasell-acp-woocommerce') . '</p>';
            return;
        }

        ?>
        <p><strong><?php esc_html_e('ACP Session ID:', 'instasell-acp-woocommerce'); ?></strong></p>
        <p><code><?php echo esc_html($acp_session_id); ?></code></p>

        <?php if ($acp_payment_intent_id): ?>
        <p><strong><?php esc_html_e('Payment Intent ID:', 'instasell-acp-woocommerce'); ?></strong></p>
        <p><code><?php echo esc_html($acp_payment_intent_id); ?></code></p>
        <?php endif; ?>

        <p><em><?php esc_html_e('This order was created through OpenAI\'s "Buy it in ChatGPT" feature.', 'instasell-acp-woocommerce'); ?></em></p>
        <?php
    }

    /**
     * Test API connection
     */
    public function test_connection() {
        check_ajax_referer('instsl_admin_nonce', 'nonce');

        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';

        if ($type === 'openai') {
            $api_key = get_option('instsl_openai_api_key');
            if (empty($api_key)) {
                wp_send_json_error('OpenAI API key is not set.');
            }

            // TODO: Implement OpenAI API connection test
            wp_send_json_success();
        } elseif ($type === 'stripe') {
            $secret_key = get_option('instsl_stripe_secret_key');
            if (empty($secret_key)) {
                wp_send_json_error('Stripe secret key is not set.');
            }

            // TODO: Implement Stripe API connection test
            wp_send_json_success();
        }

        wp_send_json_error('Invalid API type.');
    }
}
