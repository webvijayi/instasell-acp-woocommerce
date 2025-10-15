=== WooCommerce ACP Instant Checkout ===
Contributors: lokeshmotwani
Tags: woocommerce, openai, chatgpt, acp, agentic-commerce, checkout
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 8.0
License: Apache-2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

Enable OpenAI's Agentic Commerce Protocol (ACP) for any WooCommerce store, allowing "Buy it in ChatGPT" functionality.

== Description ==

This plugin enables any WooCommerce-powered website to participate in **OpenAI’s Agentic Commerce Protocol (ACP)**—the open standard that powers **“Buy it in ChatGPT” and other AI agent-driven checkouts**.

**Key Features:**

*   **Turns Your Store Into an AI-Ready Merchant:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Connects Directly With ChatGPT:** Your products can be surfaced in ChatGPT search results, and users can complete purchases natively within the chat experience.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.

== Installation ==

1.  Upload the plugin files to the `/wp-content/plugins/woocommerce-acp-instant-checkout` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Go to **WooCommerce > Settings > ACP/ChatGPT** to configure the plugin.

== Frequently Asked Questions ==

= What is the Agentic Commerce Protocol (ACP)? =

ACP is an open standard created by OpenAI to allow AI agents, like ChatGPT, to interact with e-commerce stores in a standardized way. This enables features like "Buy it in ChatGPT."

= Do I need a Stripe account? =

Yes, a Stripe account is required for payment processing.

= Where can I find my product feed? =

Your product feed is available at `https://yourdomain.com/wp-json/wcacp/v1/product-feed`.

== Screenshots ==

1.  The plugin settings page in the WooCommerce dashboard.
2.  An example of an order placed through ACP, as seen in the WooCommerce order details.

== Changelog ==

= 1.0.0 =
*   Initial release.
