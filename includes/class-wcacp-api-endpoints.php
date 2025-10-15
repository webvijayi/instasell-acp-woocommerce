<?php
/**
 * REST API Endpoints for ACP
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WCACP_API_Endpoints
 */
class WCACP_API_Endpoints {

    /**
     * Plugin instance
     */
    private $plugin;

    /**
     * Checkout session handler
     */
    private $checkout_session;

    /**
     * Constructor
     */
    public function __construct() {
        $this->plugin = woocommerce_acp_instant_checkout();
        $this->checkout_session = new WCACP_Checkout_Session();
    }

    /**
     * Get API endpoints
     */
    public function get_endpoints() {
        return array(
            array(
                'route' => '/checkout_sessions',
                'methods' => 'POST',
                'callback' => array($this, 'create_checkout_session'),
                'permission_callback' => array($this, 'validate_acp_request'),
            ),
            array(
                'route' => '/checkout_sessions/(?P<session_id>[\w-]+)',
                'methods' => 'POST',
                'callback' => array($this, 'update_checkout_session'),
                'permission_callback' => array($this, 'validate_acp_request'),
                'args' => array(
                    'session_id' => array(
                        'required' => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
            array(
                'route' => '/checkout_sessions/(?P<session_id>[\w-]+)',
                'methods' => 'GET',
                'callback' => array($this, 'get_checkout_session'),
                'permission_callback' => array($this, 'validate_acp_request'),
                'args' => array(
                    'session_id' => array(
                        'required' => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
            array(
                'route' => '/checkout_sessions/(?P<session_id>[\w-]+)/complete',
                'methods' => 'POST',
                'callback' => array($this, 'complete_checkout_session'),
                'permission_callback' => array($this, 'validate_acp_request'),
                'args' => array(
                    'session_id' => array(
                        'required' => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
            array(
                'route' => '/checkout_sessions/(?P<session_id>[\w-]+)/cancel',
                'methods' => 'POST',
                'callback' => array($this, 'cancel_checkout_session'),
                'permission_callback' => array($this, 'validate_acp_request'),
                'args' => array(
                    'session_id' => array(
                        'required' => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
        );
    }

    /**
     * Validate ACP request
     */
    public function validate_acp_request($request) {
        return $this->plugin->validate_acp_request($request);
    }

    /**
     * Create checkout session
     */
    public function create_checkout_session($request) {
        $request_data = $request->get_json_params();

        // Log request
        $this->plugin->log_acp_request('/checkout_sessions', $request_data);

        // Create session
        $response = $this->checkout_session->create_checkout_session($request_data);

        if (is_wp_error($response)) {
            return new WP_REST_Response($response->get_error_message(), $response->get_error_code());
        }

        // Log response
        $this->plugin->log_acp_request('/checkout_sessions', $request_data, $response);

        return new WP_REST_Response($response, 201);
    }

    /**
     * Update checkout session
     */
    public function update_checkout_session($request) {
        $session_id = $request->get_param('session_id');
        $request_data = $request->get_json_params();

        // Log request
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}", $request_data);

        // Update session
        $response = $this->checkout_session->update_checkout_session($session_id, $request_data);

        if (is_wp_error($response)) {
            return new WP_REST_Response($response->get_error_message(), $response->get_error_code());
        }

        // Log response
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}", $request_data, $response);

        return new WP_REST_Response($response, 200);
    }

    /**
     * Get checkout session
     */
    public function get_checkout_session($request) {
        $session_id = $request->get_param('session_id');

        // Log request
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}", array());

        // Get session
        $response = $this->checkout_session->get_checkout_session($session_id);

        if (is_wp_error($response)) {
            return new WP_REST_Response($response->get_error_message(), $response->get_error_code());
        }

        // Log response
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}", array(), $response);

        return new WP_REST_Response($response, 200);
    }

    /**
     * Complete checkout session
     */
    public function complete_checkout_session($request) {
        $session_id = $request->get_param('session_id');
        $request_data = $request->get_json_params();

        // Log request
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}/complete", $request_data);

        // Complete session
        $response = $this->checkout_session->complete_checkout_session($session_id, $request_data);

        if (is_wp_error($response)) {
            return new WP_REST_Response($response->get_error_message(), $response->get_error_code());
        }

        // Log response
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}/complete", $request_data, $response);

        return new WP_REST_Response($response, 200);
    }

    /**
     * Cancel checkout session
     */
    public function cancel_checkout_session($request) {
        $session_id = $request->get_param('session_id');

        // Log request
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}/cancel", array());

        // Cancel session
        $response = $this->checkout_session->cancel_checkout_session($session_id);

        if (is_wp_error($response)) {
            return new WP_REST_Response($response->get_error_message(), $response->get_error_code());
        }

        // Log response
        $this->plugin->log_acp_request("/checkout_sessions/{$session_id}/cancel", array(), $response);

        return new WP_REST_Response($response, 200);
    }
}
