<?php
/**
 * Admin Interface for ACP Settings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ICVAAC_Admin
 */
class ICVAAC_Admin {

    /**
     * Constructor
     */
    public function __construct() {
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
        add_action('wp_ajax_icvaac_test_connection', array($this, 'test_connection'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Instant Checkout ACP', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            __('Instant Checkout ACP', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            'manage_options',
            'icvaac-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'icvaac_settings',
            'icvaac_api_enabled',
            array(
                'type' => 'boolean',
                'default' => true,
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );

        register_setting(
            'icvaac_settings',
            'icvaac_stripe_publishable_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'icvaac_settings',
            'icvaac_stripe_secret_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'icvaac_settings',
            'icvaac_openai_api_key',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        register_setting(
            'icvaac_settings',
            'icvaac_webhook_secret',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        // Add settings sections
        add_settings_section(
            'icvaac_api_settings',
            __('API Settings', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'api_settings_section_callback'),
            'icvaac_settings'
        );

        add_settings_section(
            'icvaac_payment_settings',
            __('Payment Settings', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'payment_settings_section_callback'),
            'icvaac_settings'
        );

        // Add settings fields
        add_settings_field(
            'icvaac_api_enabled',
            __('Enable ACP API', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'api_enabled_field_callback'),
            'icvaac_settings',
            'icvaac_api_settings'
        );

        add_settings_field(
            'icvaac_openai_api_key',
            __('OpenAI API Key', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'openai_api_key_field_callback'),
            'icvaac_settings',
            'icvaac_api_settings'
        );

        add_settings_field(
            'icvaac_stripe_publishable_key',
            __('Stripe Publishable Key', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'stripe_publishable_key_field_callback'),
            'icvaac_settings',
            'icvaac_payment_settings'
        );

        add_settings_field(
            'icvaac_stripe_secret_key',
            __('Stripe Secret Key', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'stripe_secret_key_field_callback'),
            'icvaac_settings',
            'icvaac_payment_settings'
        );

        add_settings_field(
            'icvaac_webhook_secret',
            __('Stripe Webhook Secret', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'webhook_secret_field_callback'),
            'icvaac_settings',
            'icvaac_payment_settings'
        );
    }

    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap icvaac-settings-wrap">
            <!-- Header -->
            <div class="icvaac-header">
                <div class="icvaac-header-content">
                    <h1 class="icvaac-title">
                        <span class="dashicons dashicons-cart icvaac-icon"></span>
                        <?php esc_html_e('Instant Checkout via ACP Agentic Commerce', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                    </h1>
                    <p class="icvaac-subtitle">
                        <?php esc_html_e('Enable "Buy it in ChatGPT" for your WooCommerce store using OpenAI\'s Agentic Commerce Protocol', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                    </p>
                </div>
            </div>

            <!-- Two column layout -->
            <div class="icvaac-layout">
                <!-- Main Content -->
                <div class="icvaac-main-content">
                    <div class="icvaac-card">
                        <h2><?php esc_html_e('Configuration', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></h2>
                        <form method="post" action="options.php">
                            <?php
                            settings_fields('icvaac_settings');
                            do_settings_sections('icvaac_settings');
                            submit_button(__('Save Settings', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'), 'primary large');
                            ?>
                        </form>
                    </div>

                    <!-- Setup Instructions -->
                    <div class="icvaac-card">
                        <h2><?php esc_html_e('Setup Instructions', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></h2>
                        <ol class="icvaac-steps">
                            <li>
                                <strong><?php esc_html_e('Apply for OpenAI ACP Approval', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                                <p>
                                    <?php
                                    /* translators: %s: URL to OpenAI ACP application page */
                                    printf( esc_html__( 'Visit %s to apply for approval.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce' ), '<a href="https://openai.com/index/buy-it-in-chatgpt/" target="_blank" rel="noopener">OpenAI ACP Application</a>' );
                                    ?>
                                </p>
                            </li>
                            <li>
                                <strong><?php esc_html_e('Get Your API Keys', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                                <p><?php esc_html_e('After approval, copy your ACP OpenAI API Key from the OpenAI ACP dashboard and your Stripe API keys from Stripe.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
                            </li>
                            <li>
                                <strong><?php esc_html_e('Configure Settings', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                                <p><?php esc_html_e('Enable the ACP API above and paste your OpenAI API Key and Stripe keys.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
                            </li>
                            <li>
                                <strong><?php esc_html_e('Submit Product Feed', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                                <p><?php esc_html_e('Submit your Product Feed URL to OpenAI for indexing (see sidebar).', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
                            </li>
                            <li>
                                <strong><?php esc_html_e('Test Integration', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                                <p><?php esc_html_e('Test your integration using OpenAI\'s developer tools or ChatGPT.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="icvaac-sidebar">
                    <!-- API Endpoints -->
                    <div class="icvaac-card icvaac-card-info">
                        <h3><?php esc_html_e('API Endpoints', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></h3>

                        <div class="icvaac-endpoint">
                            <label><?php esc_html_e('Product Feed URL', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></label>
                            <div class="icvaac-url-box">
                                <code id="icvaac-product-feed-url"><?php echo esc_url(rest_url('icvaac/v1/product-feed')); ?></code>
                                <button type="button" class="button button-small icvaac-copy-btn" data-target="icvaac-product-feed-url">
                                    <span class="dashicons dashicons-clipboard"></span>
                                    <?php esc_html_e('Copy', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                </button>
                            </div>
                            <p class="description"><?php esc_html_e('Submit this URL to OpenAI for product indexing.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
                        </div>

                        <div class="icvaac-endpoint">
                            <label><?php esc_html_e('Stripe Webhook URL', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></label>
                            <div class="icvaac-url-box">
                                <code id="icvaac-webhook-url"><?php echo esc_url(rest_url('icvaac/v1/webhooks/stripe')); ?></code>
                                <button type="button" class="button button-small icvaac-copy-btn" data-target="icvaac-webhook-url">
                                    <span class="dashicons dashicons-clipboard"></span>
                                    <?php esc_html_e('Copy', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                </button>
                            </div>
                            <p class="description">
                                <?php esc_html_e('Add this in your Stripe dashboard.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                <br>
                                <?php esc_html_e('Events: payment_intent.succeeded, payment_intent.payment_failed', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Resources -->
                    <div class="icvaac-card icvaac-card-primary">
                        <h3><?php esc_html_e('Resources', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></h3>
                        <ul class="icvaac-resources">
                            <li>
                                <span class="dashicons dashicons-book"></span>
                                <a href="https://github.com/webvijayi/instant-checkout-via-acp-agentic-commerce-for-woocommerce" target="_blank" rel="noopener">
                                    <?php esc_html_e('Plugin Documentation', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                </a>
                            </li>
                            <li>
                                <span class="dashicons dashicons-admin-generic"></span>
                                <a href="https://openai.com/index/buy-it-in-chatgpt/" target="_blank" rel="noopener">
                                    <?php esc_html_e('OpenAI ACP Documentation', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                </a>
                            </li>
                            <li>
                                <span class="dashicons dashicons-media-code"></span>
                                <a href="https://stripe.com/docs" target="_blank" rel="noopener">
                                    <?php esc_html_e('Stripe Documentation', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Support & Review -->
                    <div class="icvaac-card icvaac-card-success">
                        <h3><?php esc_html_e('Support This Plugin', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></h3>
                        <p><?php esc_html_e('If you find this plugin helpful, please consider leaving a review!', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
                        <a href="https://wordpress.org/support/plugin/instant-checkout-via-acp-agentic-commerce-for-woocommerce/reviews/#new-post" target="_blank" rel="noopener" class="button button-primary icvaac-review-btn">
                            <span class="dashicons dashicons-star-filled"></span>
                            <?php esc_html_e('Leave a Review', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                        </a>
                        <p class="icvaac-small-text">
                            <a href="https://github.com/webvijayi/instant-checkout-via-acp-agentic-commerce-for-woocommerce/issues" target="_blank" rel="noopener">
                                <?php esc_html_e('Need help? Report an issue', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                            </a>
                        </p>
                    </div>

                    <!-- Plugin Info -->
                    <div class="icvaac-card icvaac-card-light">
                        <p class="icvaac-version">
                            <strong><?php esc_html_e('Version:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong> <?php echo esc_html(ICVAAC_VERSION); ?>
                        </p>
                        <p class="icvaac-author">
                            <strong><?php esc_html_e('By:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                            <a href="https://webvijayi.com" target="_blank" rel="noopener">Web Vijayi</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * API settings section callback
     */
    public function api_settings_section_callback() {
        echo '<p>' . esc_html__('Enable or disable the ACP API and enter your OpenAI API key.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce') . '</p>';
    }

    /**
     * Payment settings section callback
     */
    public function payment_settings_section_callback() {
        echo '<p>' . esc_html__('Enter your Stripe API keys and webhook secret to enable secure payments.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce') . '</p>';
    }

    /**
     * API enabled field callback
     */
    public function api_enabled_field_callback() {
        $value = get_option('icvaac_api_enabled', '1');
        ?>
        <input type="checkbox" name="icvaac_api_enabled" value="1" <?php checked($value, '1'); ?> />
        <label for="icvaac_api_enabled"><?php esc_html_e('Enable ACP API endpoints', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></label>
        <?php
    }

    /**
     * OpenAI API key field callback
     */
    public function openai_api_key_field_callback() {
        $value = get_option('icvaac_openai_api_key', '');
        ?>
        <input type="password" name="icvaac_openai_api_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('API key for authenticating ACP requests from OpenAI (received after OpenAI approval).', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
        <button type="button" class="button button-secondary icvaac-test-connection" data-type="openai"><?php esc_html_e('Test API', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></button>
        <?php
    }

    /**
     * Stripe publishable key field callback
     */
    public function stripe_publishable_key_field_callback() {
        $value = get_option('icvaac_stripe_publishable_key', '');
        ?>
        <input type="text" name="icvaac_stripe_publishable_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Your Stripe publishable key.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
        <button type="button" class="button button-secondary icvaac-test-connection" data-type="stripe"><?php esc_html_e('Test Connection', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></button>
        <?php
    }

    /**
     * Stripe secret key field callback
     */
    public function stripe_secret_key_field_callback() {
        $value = get_option('icvaac_stripe_secret_key', '');
        ?>
        <input type="password" name="icvaac_stripe_secret_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Your Stripe secret key.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
        <?php
    }

    /**
     * Webhook secret field callback
     */
    public function webhook_secret_field_callback() {
        $value = get_option('icvaac_webhook_secret', '');
        ?>
        <input type="password" name="icvaac_webhook_secret" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Stripe webhook secret for verifying webhook signatures.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_icvaac-settings') {
            return;
        }

        wp_enqueue_style(
            'icvaac-admin',
            ICVAAC_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ICVAAC_VERSION
        );

        wp_enqueue_script(
            'icvaac-admin',
            ICVAAC_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            ICVAAC_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script(
            'icvaac-admin',
            'icvaac_admin',
            array(
                'nonce' => wp_create_nonce('icvaac_admin_nonce'),
                'ajax_url' => admin_url('admin-ajax.php')
            )
        );
    }

    /**
     * Admin notices
     */
    public function admin_notices() {
        // Check if API is enabled but keys are missing
        if (get_option('icvaac_api_enabled') === '1') {
            if (empty(get_option('icvaac_openai_api_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('Instant Checkout ACP: An OpenAI API key is required for ACP functionality. Please <a href="%s">enter your API key</a>.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'), esc_url(admin_url('options-general.php?page=icvaac-settings')));
                        ?>
                    </p>
                </div>
                <?php
            }

            if (empty(get_option('icvaac_stripe_secret_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('Instant Checkout ACP: A Stripe secret key is required for payment processing. Please <a href="%s">enter your Stripe keys</a>.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'), esc_url(admin_url('options-general.php?page=icvaac-settings')));
                        ?>
                    </p>
                </div>
                <?php
            }
        }
    }

    /**
     * Add order meta boxes
     */
    public function add_order_meta_boxes() {
        add_meta_box(
            'icvaac-order-info',
            __('ACP Information', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
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
            echo '<p>' . esc_html__('This order was not created through ACP.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce') . '</p>';
            return;
        }

        ?>
        <p><strong><?php esc_html_e('ACP Session ID:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong></p>
        <p><code><?php echo esc_html($acp_session_id); ?></code></p>

        <?php if ($acp_payment_intent_id): ?>
        <p><strong><?php esc_html_e('Payment Intent ID:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong></p>
        <p><code><?php echo esc_html($acp_payment_intent_id); ?></code></p>
        <?php endif; ?>

        <p><em><?php esc_html_e('This order was created through OpenAI\'s "Buy it in ChatGPT" feature.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></em></p>
        <?php
    }

    /**
     * Test API connection
     */
    public function test_connection() {
        check_ajax_referer('icvaac_admin_nonce', 'nonce');

        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';

        if ($type === 'openai') {
            $api_key = get_option('icvaac_openai_api_key');
            if (empty($api_key)) {
                wp_send_json_error('OpenAI API key is not set.');
            }

            // TODO: Implement OpenAI API connection test
            wp_send_json_success();
        } elseif ($type === 'stripe') {
            $secret_key = get_option('icvaac_stripe_secret_key');
            if (empty($secret_key)) {
                wp_send_json_error('Stripe secret key is not set.');
            }

            // TODO: Implement Stripe API connection test
            wp_send_json_success();
        }

        wp_send_json_error('Invalid API type.');
    }
}
