<?php
/**
 * Checkout Session Handler for ACP
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WCACP_Checkout_Session
 */
class WCACP_Checkout_Session {

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
     * Initialize the checkout session handler
     */
    private function init() {
        // Hook into WooCommerce order creation for ACP orders
        add_action('woocommerce_new_order', array($this, 'handle_acp_order_creation'), 10, 2);
    }

    /**
     * Create a checkout session
     */
    public function create_checkout_session($request_data) {
        // Validate request data
        $validation = $this->validate_create_request($request_data);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Generate unique session ID
        $session_id = 'cs_' . wp_generate_uuid4();

        // Calculate line items and totals
        $line_items = array();
        $totals = array();
        $fulfillment_options = array();
        $messages = array();

        foreach ($request_data['items'] as $item) {
            $product = wc_get_product($item['id']);
            if (!$product) {
                $messages[] = array(
                    'type' => 'error',
                    'code' => 'invalid',
                    'path' => '$.items[' . array_search($item, $request_data['items']) . ']',
                    'content_type' => 'plain',
                    'content' => 'Product not found: ' . $item['id']
                );
                continue;
            }

            // Check stock
            if (!$product->is_in_stock() || $product->get_stock_quantity() < $item['quantity']) {
                $messages[] = array(
                    'type' => 'error',
                    'code' => 'out_of_stock',
                    'path' => '$.items[' . array_search($item, $request_data['items']) . ']',
                    'content_type' => 'plain',
                    'content' => 'Insufficient stock for product: ' . $product->get_name()
                );
                continue;
            }

            $line_item_id = 'li_' . wp_generate_uuid4();
            $base_amount = $product->get_price() * $item['quantity'] * 100; // Convert to cents
            $tax_rate = $this->get_tax_rate($product, $request_data);
            $tax_amount = $base_amount * ($tax_rate / 100);

            $line_items[] = array(
                'id' => $line_item_id,
                'item' => array(
                    'id' => $item['id'],
                    'quantity' => $item['quantity']
                ),
                'base_amount' => (int) $base_amount,
                'discount' => 0, // Could implement discounts here
                'subtotal' => (int) ($base_amount), // After discount
                'tax' => (int) $tax_amount,
                'total' => (int) ($base_amount + $tax_amount)
            );
        }

        // Calculate totals
        $items_base_amount = array_sum(array_column($line_items, 'base_amount'));
        $items_discount = array_sum(array_column($line_items, 'discount'));
        $subtotal = $items_base_amount - $items_discount;
        $total_tax = array_sum(array_column($line_items, 'tax'));
        $total_amount = $subtotal + $total_tax;

        $totals = array(
            array(
                'type' => 'items_base_amount',
                'display_text' => 'Item(s) total',
                'amount' => $items_base_amount
            ),
            array(
                'type' => 'subtotal',
                'display_text' => 'Subtotal',
                'amount' => $subtotal
            ),
            array(
                'type' => 'tax',
                'display_text' => 'Tax',
                'amount' => $total_tax
            ),
            array(
                'type' => 'total',
                'display_text' => 'Total',
                'amount' => $total_amount
            )
        );

        // Get fulfillment options if address provided
        if (isset($request_data['fulfillment_address'])) {
            $fulfillment_options = $this->get_fulfillment_options($request_data['fulfillment_address'], $line_items);

            if (!empty($fulfillment_options)) {
                $status = 'ready_for_payment';
            } else {
                $status = 'in_progress';
                $messages[] = array(
                    'type' => 'error',
                    'code' => 'invalid',
                    'content_type' => 'plain',
                    'content' => 'No fulfillment options available for this address'
                );
            }
        } else {
            $status = 'in_progress';
        }

        // Store session in a custom post type
        $session_post_id = wp_insert_post(array(
            'post_title' => $session_id,
            'post_type' => 'acp_checkout_session',
            'post_status' => 'publish',
        ));

        if (is_wp_error($session_post_id)) {
            return $session_post_id;
        }

        update_post_meta($session_post_id, '_acp_session_id', $session_id);
        update_post_meta($session_post_id, '_acp_status', $status);
        update_post_meta($session_post_id, '_acp_request_data', $request_data);
        update_post_meta($session_post_id, '_acp_line_items', $line_items);
        update_post_meta($session_post_id, '_acp_totals', $totals);
        update_post_meta($session_post_id, '_acp_fulfillment_options', $fulfillment_options);
        update_post_meta($session_post_id, '_acp_messages', $messages);

        // Prepare response
        $response = array(
            'id' => $session_id,
            'status' => $status,
            'currency' => get_woocommerce_currency(),
            'payment_provider' => array(
                'provider' => 'stripe',
                'supported_payment_methods' => array('card')
            ),
            'line_items' => $line_items,
            'totals' => $totals,
            'fulfillment_options' => $fulfillment_options,
            'messages' => $messages,
            'links' => array(
                array(
                    'type' => 'terms_of_use',
                    'url' => get_permalink(wc_get_page_id('terms'))
                )
            )
        );

        // Add fulfillment address if provided
        if (isset($request_data['fulfillment_address'])) {
            $response['fulfillment_address'] = $request_data['fulfillment_address'];
        }

        return $response;
    }

    /**
     * Update checkout session
     */
    public function update_checkout_session($session_id, $request_data) {
        $session_post = $this->get_session_post($session_id);

        if (!$session_post) {
            return new WP_Error('session_not_found', 'Checkout session not found', array('status' => 404));
        }

        // Update fulfillment option if provided
        if (isset($request_data['fulfillment_option_id'])) {
            update_post_meta($session_post->ID, '_acp_selected_fulfillment_option', $request_data['fulfillment_option_id']);
        }

        return $this->get_checkout_session($session_id);
    }

    /**
     * Complete checkout session
     */
    public function complete_checkout_session($session_id, $request_data) {
        $session_post = $this->get_session_post($session_id);

        if (!$session_post) {
            return new WP_Error('session_not_found', 'Checkout session not found', array('status' => 404));
        }

        $session_data = $this->get_session_data_from_post($session_post);

        // Validate payment data
        if (!isset($request_data['payment_data'])) {
            return new WP_Error('missing_payment_data', 'Payment data is required', array('status' => 400));
        }

        // Process payment through Stripe
        $payment_result = $this->process_payment($session_id, $request_data['payment_data'], $session_data);

        if (is_wp_error($payment_result)) {
            return $payment_result;
        }

        // Create WooCommerce order
        $order_result = $this->create_woocommerce_order($session_id, $session_data, $payment_result);

        if (is_wp_error($order_result)) {
            return $order_result;
        }

        // Update session status
        update_post_meta($session_post->ID, '_acp_status', 'completed');
        update_post_meta($session_post->ID, '_acp_order_id', $order_result['order_id']);

        return array(
            'id' => $session_id,
            'status' => 'completed',
            'order_id' => $order_result['order_id']
        );
    }

    /**
     * Get checkout session
     */
    public function get_checkout_session($session_id) {
        $session_post = $this->get_session_post($session_id);

        if (!$session_post) {
            return new WP_Error('session_not_found', 'Checkout session not found', array('status' => 404));
        }

        $session_data = $this->get_session_data_from_post($session_post);

        return array(
            'id' => $session_id,
            'status' => get_post_meta($session_post->ID, '_acp_status', true),
            'currency' => get_woocommerce_currency(),
            'payment_provider' => array(
                'provider' => 'stripe',
                'supported_payment_methods' => array('card')
            ),
            'line_items' => $session_data['line_items'],
            'totals' => $session_data['totals'],
            'fulfillment_options' => $session_data['fulfillment_options'],
            'messages' => $session_data['messages'],
            'links' => array(
                array(
                    'type' => 'terms_of_use',
                    'url' => get_permalink(wc_get_page_id('terms'))
                )
            )
        );
    }

    /**
     * Cancel checkout session
     */
    public function cancel_checkout_session($session_id) {
        $session_post = $this->get_session_post($session_id);

        if ($session_post) {
            update_post_meta($session_post->ID, '_acp_status', 'canceled');
        }

        return array(
            'id' => $session_id,
            'status' => 'canceled'
        );
    }

    /**
     * Validate create request
     */
    private function validate_create_request($request_data) {
        if (!isset($request_data['items']) || empty($request_data['items'])) {
            return new WP_Error('missing_items', 'Items are required', array('status' => 400));
        }

        foreach ($request_data['items'] as $item) {
            if (!isset($item['id']) || !isset($item['quantity'])) {
                return new WP_Error('invalid_item', 'Item must have id and quantity', array('status' => 400));
            }
        }

        return true;
    }

    /**
     * Get tax rate for product and address
     */
    private function get_tax_rate($product, $request_data) {
        // Default tax rate
        $tax_rate = 0;

        // If address is provided, calculate tax based on location
        if (isset($request_data['fulfillment_address'])) {
            $address = $request_data['fulfillment_address'];
            $country = isset($address['country']) ? $address['country'] : '';
            $state = isset($address['state']) ? $address['state'] : '';
            $postcode = isset($address['postal_code']) ? $address['postal_code'] : '';
            $city = isset($address['city']) ? $address['city'] : '';

            // Get WooCommerce tax rates
            $tax_class = $product->get_tax_class();
            $tax_rates = WC_Tax::find_rates(array(
                'country' => $country,
                'state' => $state,
                'postcode' => $postcode,
                'city' => $city,
                'tax_class' => $tax_class,
            ));

            if (!empty($tax_rates)) {
                $tax_rate = array_sum(wp_list_pluck($tax_rates, 'rate'));
            }
        }

        return $tax_rate;
    }

    /**
     * Get session post by session ID
     */
    private function get_session_post($session_id) {
        $posts = get_posts(array(
            'post_type' => 'acp_checkout_session',
            'meta_key' => '_acp_session_id',
            'meta_value' => $session_id,
            'posts_per_page' => 1,
        ));

        return !empty($posts) ? $posts : null;
    }

    private function get_session_data_from_post($post) {
        return array(
            'line_items' => get_post_meta($post->ID, '_acp_line_items', true),
            'totals' => get_post_meta($post->ID, '_acp_totals', true),
            'fulfillment_options' => get_post_meta($post->ID, '_acp_fulfillment_options', true),
            'messages' => get_post_meta($post->ID, '_acp_messages', true),
        );
    }

    /**
     * Get fulfillment options
     */
    private function get_fulfillment_options($address, $line_items) {
        $options = array();

        // Digital products - no shipping needed
        $has_digital = false;
        foreach ($line_items as $line_item) {
            $product = wc_get_product($line_item['item']['id']);
            if ($product && $product->is_virtual()) {
                $has_digital = true;
                break;
            }
        }

        if ($has_digital) {
            $options[] = array(
                'type' => 'digital',
                'id' => 'digital_delivery',
                'title' => 'Digital Delivery',
                'subtitle' => 'Available immediately after purchase',
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0
            );
        }

        // Physical products - shipping options
        $has_physical = false;
        foreach ($line_items as $line_item) {
            $product = wc_get_product($line_item['item']['id']);
            if ($product && !$product->is_virtual()) {
                $has_physical = true;
                break;
            }
        }

        if ($has_physical) {
            $options[] = array(
                'type' => 'shipping',
                'id' => 'standard_shipping',
                'title' => 'Standard Shipping',
                'subtitle' => 'Arrives in 4-5 business days',
                'carrier' => 'USPS',
                'earliest_delivery_time' => gmdate('Y-m-d\TH:i:s\Z', strtotime('+4 days')),
                'latest_delivery_time' => gmdate('Y-m-d\TH:i:s\Z', strtotime('+5 days')),
                'subtotal' => 500, // $5.00 in cents
                'tax' => 0,
                'total' => 500
            );

            $options[] = array(
                'type' => 'shipping',
                'id' => 'express_shipping',
                'title' => 'Express Shipping',
                'subtitle' => 'Arrives in 1-2 business days',
                'carrier' => 'FedEx',
                'earliest_delivery_time' => gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 day')),
                'latest_delivery_time' => gmdate('Y-m-d\TH:i:s\Z', strtotime('+2 days')),
                'subtotal' => 1500, // $15.00 in cents
                'tax' => 0,
                'total' => 1500
            );
        }

        return $options;
    }

    /**
     * Process payment through Stripe
     */
    private function process_payment($session_id, $payment_data, $session_data) {
        // Load Stripe
        if (!class_exists('\Stripe\Stripe')) {
            require_once $this->plugin->get_plugin_dir() . 'vendor/autoload.php';
        }

        \Stripe\Stripe::setApiKey(get_option('wcacp_stripe_secret_key'));

        try {
            // Create payment intent
            $payment_intent = \Stripe\PaymentIntent::create([
                'amount' => $session_data['totals'][array_search('total', array_column($session_data['totals'], 'type'))]['amount'],
                'currency' => strtolower(get_woocommerce_currency()),
                'payment_method' => $payment_data['payment_token'],
                'confirm' => true,
                'return_url' => home_url(),
                'metadata' => [
                    'acp_session_id' => $session_id
                ]
            ]);

            return array(
                'payment_intent_id' => $payment_intent->id,
                'status' => $payment_intent->status
            );

        } catch (Exception $e) {
            return new WP_Error('payment_failed', 'Payment processing failed: ' . $e->getMessage(), array('status' => 402));
        }
    }

    /**
     * Create WooCommerce order
     */
    private function create_woocommerce_order($session_id, $session_data, $payment_result) {
        // Create WooCommerce order
        $order = wc_create_order();

        // Add products to order
        foreach ($session_data['line_items'] as $line_item) {
            $product = wc_get_product($line_item['item']['id']);
            if ($product) {
                $order->add_product(
                    $product,
                    $line_item['item']['quantity'],
                    array(
                        'subtotal' => $line_item['base_amount'] / 100, // Convert from cents
                        'total' => $line_item['total'] / 100
                    )
                );
            }
        }

        // Set shipping if applicable
        if (isset($session_data['selected_fulfillment_option'])) {
            $order->add_shipping(
                new WC_Shipping_Rate(
                    'acp_shipping',
                    'ACP Shipping',
                    $session_data['totals'][array_search('fulfillment', array_column($session_data['totals'], 'type'))]['amount'] / 100,
                    0,
                    'flat_rate'
                )
            );
        }

        // Calculate totals
        $order->calculate_totals();

        // Set payment method
        $order->set_payment_method('stripe');
        $order->set_payment_method_title('Stripe (ACP)');

        // Add ACP metadata
        $order->update_meta_data('_acp_session_id', $session_id);
        $order->update_meta_data('_acp_payment_intent_id', $payment_result['payment_intent_id']);

        // Save order
        $order->save();

        return array(
            'order_id' => $order->get_id(),
            'order' => $order
        );
    }

    /**
     * Handle ACP order creation
     */
    public function handle_acp_order_creation($order_id, $order) {
        // Mark ACP orders with a special flag
        if ($order->get_meta('_acp_session_id')) {
            $order->update_meta_data('_acp_order', 'yes');
            $order->save();
        }
    }
}
