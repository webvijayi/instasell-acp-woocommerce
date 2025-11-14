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
        // Add admin menu (priority 99 to load after WooCommerce core menus)
        add_action('admin_menu', array($this, 'add_admin_menu'), 99);

        // Add settings sections
        add_action('admin_init', array($this, 'register_settings'));

        // Redirect old settings URL to new location
        add_action('admin_init', array($this, 'redirect_old_settings_url'));

        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Add admin notices
        add_action('admin_notices', array($this, 'admin_notices'));

        // Add meta boxes for ACP orders
        add_action('add_meta_boxes', array($this, 'add_order_meta_boxes'));

        // Add meta box for products
        add_action('add_meta_boxes', array($this, 'add_product_meta_boxes'));

        // Save product meta
        add_action('woocommerce_process_product_meta', array($this, 'save_product_meta'));

        // Add bulk actions for products
        add_filter('bulk_actions-edit-product', array($this, 'add_bulk_actions'));
        add_filter('handle_bulk_actions-edit-product', array($this, 'handle_bulk_actions'), 10, 3);

        // Add AJAX action for testing connections
        add_action('wp_ajax_icvaac_test_connection', array($this, 'test_connection'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('AI Checkout', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            __('AI Checkout', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            'manage_woocommerce',
            'icvaac-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Redirect old settings URL to new location
     */
    public function redirect_old_settings_url() {
        // Check if user is on old settings page
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- URL parameter check for redirect only, no data processing
        if (isset($_GET['page']) && sanitize_text_field(wp_unslash($_GET['page'])) === 'icvaac-settings') {
            $current_screen = get_current_screen();
            // If on settings page (not WooCommerce submenu), redirect
            if ($current_screen && strpos($current_screen->id, 'settings_page') !== false) {
                wp_safe_redirect(admin_url('admin.php?page=icvaac-settings'));
                exit;
            }
        }
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
            'icvaac_test_mode',
            array(
                'type' => 'boolean',
                'default' => false,
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
            'icvaac_test_mode',
            __('Test Mode', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'test_mode_field_callback'),
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

                    <!-- OpenAI Bots Setup -->
                    <div class="icvaac-card icvaac-card-warning">
                        <h3><?php esc_html_e('OpenAI Bots Setup', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></h3>
                        <p><?php esc_html_e('Allow OpenAI bots to crawl your product feed by adding these rules to your robots.txt:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></p>

                        <div class="icvaac-robots-txt-box" style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin: 10px 0;">
                            <code id="icvaac-robots-txt" style="display: block; white-space: pre-wrap; font-size: 12px; line-height: 1.5;">
# Allow OpenAI Bots
User-agent: OAI-SearchBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: GPTBot
Allow: /</code>
                            <button type="button" class="button button-small icvaac-copy-btn" data-target="icvaac-robots-txt" style="margin-top: 10px;">
                                <span class="dashicons dashicons-clipboard"></span>
                                <?php esc_html_e('Copy Rules', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                            </button>
                        </div>

                        <p class="description" style="margin-top: 10px;">
                            <strong><?php esc_html_e('How to add these rules:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                        </p>

                        <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                            <li><?php esc_html_e('Edit your robots.txt file via FTP/cPanel', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></li>
                            <li>
                                <?php esc_html_e('Or use your SEO plugin:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                                <ul style="padding-left: 20px; margin: 5px 0;">
                                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=wpseo_tools&tool=file-editor')); ?>" target="_blank">Yoast SEO</a></li>
                                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=rank-math&view=file_editor')); ?>" target="_blank">Rank Math</a></li>
                                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=aioseo-tools&aioseo-tab=robots-txt')); ?>" target="_blank">All in One SEO</a></li>
                                </ul>
                            </li>
                        </ul>

                        <p class="description">
                            <strong><?php esc_html_e('Note:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                            <?php esc_html_e('If you use an SEO plugin, manage robots.txt through that plugin to avoid conflicts.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                        </p>
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
     * Test mode field callback
     */
    public function test_mode_field_callback() {
        $value = get_option('icvaac_test_mode', '0');
        ?>
        <input type="checkbox" name="icvaac_test_mode" value="1" <?php checked($value, '1'); ?> />
        <label for="icvaac_test_mode"><?php esc_html_e('Enable test mode (orders will be marked as test orders)', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></label>
        <p class="description">
            <?php esc_html_e('When enabled, all ACP checkout sessions will create test orders. Test orders are marked with a special order note and meta for easy identification. Use this for testing before going live.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
        </p>
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
        // Check for both old and new hook names for backward compatibility
        if ($hook !== 'woocommerce_page_icvaac-settings' && $hook !== 'settings_page_icvaac-settings') {
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
        // Show bulk action success messages
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin notice display only, no data processing
        if (isset($_GET['icvaac_bulk_action']) && isset($_GET['icvaac_count'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin notice display only, no data processing
            $action = sanitize_text_field(wp_unslash($_GET['icvaac_bulk_action']));
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin notice display only, no data processing
            $count = absint($_GET['icvaac_count']);

            if ($action === 'enabled') {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        /* translators: %d: Number of products enabled */
                        printf(esc_html(_n('%d product enabled for ChatGPT checkout.', '%d products enabled for ChatGPT checkout.', $count, 'instant-checkout-via-acp-agentic-commerce-for-woocommerce')), absint($count));
                        ?>
                    </p>
                </div>
                <?php
            } elseif ($action === 'disabled') {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        /* translators: %d: Number of products disabled */
                        printf(esc_html(_n('%d product disabled for ChatGPT checkout.', '%d products disabled for ChatGPT checkout.', $count, 'instant-checkout-via-acp-agentic-commerce-for-woocommerce')), absint($count));
                        ?>
                    </p>
                </div>
                <?php
            }
        }

        // Show warning if test mode is enabled
        if (get_option('icvaac_test_mode') === '1') {
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php esc_html_e('Instant Checkout ACP - Test Mode Enabled:', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                    <?php esc_html_e('All ACP orders will be marked as test orders. Disable test mode when you\'re ready to accept live orders.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=icvaac-settings')); ?>"><?php esc_html_e('Manage Settings', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></a>
                </p>
            </div>
            <?php
        }

        // Check if API is enabled but keys are missing
        if (get_option('icvaac_api_enabled') === '1') {
            if (empty(get_option('icvaac_openai_api_key'))) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <?php
                        /* translators: %s: URL to plugin settings page */
                        printf(esc_html__('Instant Checkout ACP: An OpenAI API key is required for ACP functionality. Please <a href="%s">enter your API key</a>.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'), esc_url(admin_url('admin.php?page=icvaac-settings')));
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
                        printf(esc_html__('Instant Checkout ACP: A Stripe secret key is required for payment processing. Please <a href="%s">enter your Stripe keys</a>.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'), esc_url(admin_url('admin.php?page=icvaac-settings')));
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
        $is_test_order = get_post_meta($post->ID, '_icvaac_test_order', true);

        if (!$acp_session_id) {
            echo '<p>' . esc_html__('This order was not created through ACP.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce') . '</p>';
            return;
        }

        // Show test order warning
        if ($is_test_order === 'yes') {
            ?>
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin-bottom: 15px;">
                <strong style="color: #856404;">⚠️ <?php esc_html_e('TEST ORDER', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?></strong>
                <p style="margin: 5px 0 0 0; color: #856404;">
                    <?php esc_html_e('This order was created in test mode. It is not a real customer order.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                </p>
            </div>
            <?php
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
     * Add product meta boxes
     */
    public function add_product_meta_boxes() {
        add_meta_box(
            'icvaac-product-settings',
            __('ChatGPT Checkout', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'),
            array($this, 'product_meta_box_callback'),
            'product',
            'side',
            'default'
        );
    }

    /**
     * Product meta box callback
     */
    public function product_meta_box_callback($post) {
        // Get current value, default to checked (enabled) if not set for backward compatibility
        $enabled = get_post_meta($post->ID, '_icvaac_enable_chatgpt', true);

        // If meta doesn't exist (empty string), default to enabled
        $is_enabled = ($enabled === '' || $enabled === 'yes');

        // Add nonce for security
        wp_nonce_field('icvaac_save_product_meta', 'icvaac_product_meta_nonce');
        ?>
        <div class="icvaac-product-chatgpt-option">
            <p>
                <label>
                    <input
                        type="checkbox"
                        name="_icvaac_enable_chatgpt"
                        value="yes"
                        <?php checked($is_enabled, true); ?>
                    />
                    <?php esc_html_e('Enable this product for ChatGPT checkout', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
                </label>
            </p>
            <p class="description">
                <?php esc_html_e('When enabled, this product will appear in the product feed and can be purchased through ChatGPT conversations via OpenAI\'s Agentic Commerce Protocol.', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce'); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Save product meta
     */
    public function save_product_meta($product_id) {
        // Check nonce
        if (!isset($_POST['icvaac_product_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['icvaac_product_meta_nonce'])), 'icvaac_save_product_meta')) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_product', $product_id)) {
            return;
        }

        // Save the meta value
        if (isset($_POST['_icvaac_enable_chatgpt'])) {
            update_post_meta($product_id, '_icvaac_enable_chatgpt', 'yes');
        } else {
            // If unchecked, save 'no' explicitly
            update_post_meta($product_id, '_icvaac_enable_chatgpt', 'no');
        }
    }

    /**
     * Add bulk actions to product list
     */
    public function add_bulk_actions($actions) {
        $actions['icvaac_enable_chatgpt'] = __('Enable for ChatGPT checkout', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce');
        $actions['icvaac_disable_chatgpt'] = __('Disable for ChatGPT checkout', 'instant-checkout-via-acp-agentic-commerce-for-woocommerce');
        return $actions;
    }

    /**
     * Handle bulk actions
     */
    public function handle_bulk_actions($redirect_to, $action, $post_ids) {
        if ($action === 'icvaac_enable_chatgpt') {
            $count = 0;
            foreach ($post_ids as $post_id) {
                // Check if user has permission to edit this product
                if (current_user_can('edit_product', $post_id)) {
                    update_post_meta($post_id, '_icvaac_enable_chatgpt', 'yes');
                    $count++;
                }
            }

            // Invalidate product feed cache
            if (class_exists('ICVAAC_Product_Feed')) {
                $product_feed = new ICVAAC_Product_Feed();
                $product_feed->invalidate_feed_cache();
            }

            // Add admin notice
            $redirect_to = add_query_arg(
                array(
                    'icvaac_bulk_action' => 'enabled',
                    'icvaac_count' => $count
                ),
                $redirect_to
            );
        } elseif ($action === 'icvaac_disable_chatgpt') {
            $count = 0;
            foreach ($post_ids as $post_id) {
                // Check if user has permission to edit this product
                if (current_user_can('edit_product', $post_id)) {
                    update_post_meta($post_id, '_icvaac_enable_chatgpt', 'no');
                    $count++;
                }
            }

            // Invalidate product feed cache
            if (class_exists('ICVAAC_Product_Feed')) {
                $product_feed = new ICVAAC_Product_Feed();
                $product_feed->invalidate_feed_cache();
            }

            // Add admin notice
            $redirect_to = add_query_arg(
                array(
                    'icvaac_bulk_action' => 'disabled',
                    'icvaac_count' => $count
                ),
                $redirect_to
            );
        }

        return $redirect_to;
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
