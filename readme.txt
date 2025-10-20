=== ACP Instant Checkout for WooCommerce ===
Contributors: lokeshmotwani
Tags: chatgpt, acp, checkout, ai-commerce, stripe
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enable OpenAI's Agentic Commerce Protocol (ACP) allowing "Buy it in ChatGPT" functionality for your online store.

== Description ==

This plugin enables any WooCommerce-powered website to participate in **OpenAI’s Agentic Commerce Protocol (ACP)**—the open standard that powers **“Buy it in ChatGPT” and other AI agent-driven checkouts**.

**Key Features:**

*   **Turns Your Store Into an AI-Ready Merchant:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Connects Directly With ChatGPT:** Your products can be surfaced in ChatGPT search results, and users can complete purchases natively within the chat experience.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.
*   **Easy Integration:** Works seamlessly with your existing store setup with minimal configuration required.

== Installation ==

1.  Upload the plugin files to the `/wp-content/plugins/woocommerce-acp-instant-checkout` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Go to **WooCommerce > Settings > ACP/ChatGPT** to configure the plugin.

== Frequently Asked Questions ==

= What is the Agentic Commerce Protocol (ACP)? =

ACP is an open standard created by OpenAI to allow AI agents, like ChatGPT, to interact with e-commerce stores in a standardized way. This enables features like "Buy it in ChatGPT."

= Do I need to apply for OpenAI ACP approval? =

Yes, you need to apply at https://openai.com/index/buy-it-in-chatgpt/ and get approved before your products can appear in ChatGPT.

= Do I need a Stripe account? =

Yes, a Stripe account is required for payment processing.

= Where can I find my product feed? =

Your product feed is available at `https://yourdomain.com/wp-json/wcacp/v1/product-feed`.

= Is this plugin free? =

Yes, the plugin is completely free and open source under GPLv2 license.

= Does this work with any store? =

Yes, as long as you meet the minimum requirements (WordPress 5.0+, WooCommerce 5.0+, PHP 7.4+).

== Screenshots ==

1.  The plugin settings page in the WooCommerce dashboard.
2.  An example of an order placed through ACP, as seen in the WooCommerce order details.

== Changelog ==

= 1.0.0 =
*   Initial release.