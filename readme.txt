=== Instant Checkout via ACP Agentic Commerce for WooCommerce ===
Contributors: webvijayi
Tags: chatgpt, buy in chatgpt, ai checkout, woocommerce, openai, acp, chatgpt woocommerce, conversational commerce, ai shopping, stripe, chatgpt checkout, agentic commerce
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.1.0
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enable "Buy in ChatGPT" for WooCommerce. Let customers buy products directly through ChatGPT conversations using OpenAI's Agentic Commerce Protocol (ACP). AI-powered conversational checkout.

== Description ==

Instant Checkout ACP enables any WooCommerce-powered website to participate in the **Agentic Commerce Protocol (ACP)**—the open standard that powers **"Buy it in ChatGPT" and other AI agent-driven checkouts**.

**Developed by Web Vijayi** to help WooCommerce store owners tap into the future of AI-powered commerce.

= Key Features =

*   **AI-Ready Sales Channel:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Sell Directly via ChatGPT:** Your products can be surfaced in ChatGPT search results, and customers can complete purchases natively within the chat experience.
*   **Per-Product Control:** Choose which products are available for ChatGPT checkout with an easy checkbox on each product edit screen.
*   **Test Mode:** Safely test your integration before going live - test orders are clearly marked and identifiable.
*   **Bulk Actions:** Enable or disable multiple products for ChatGPT checkout at once from the product list.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.
*   **Easy Integration:** Works seamlessly with your existing store setup with minimal configuration required.
*   **OpenAI Bots Setup Guide:** Built-in instructions for configuring your robots.txt to allow OpenAI crawlers.

= About ACP =

The Agentic Commerce Protocol (ACP) is an open standard developed to enable AI agents to interact with e-commerce platforms. This plugin implements the ACP specification to allow your WooCommerce store to work with ChatGPT and other ACP-compatible AI agents.

= Why Instant Checkout ACP? =

Instant Checkout ACP focuses on helping store owners **sell more** by tapping into emerging AI-powered shopping channels. As AI agents become the new way customers discover and purchase products, Instant Checkout ACP ensures your WooCommerce store is ready.

== Installation ==

1.  Upload the plugin files to the `/wp-content/plugins/instant-checkout-via-acp-agentic-commerce-for-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Go to **WooCommerce > AI Checkout** to configure the plugin.

== Configuration ==

After installation:

1. Apply for ACP approval at https://openai.com/index/buy-it-in-chatgpt/
2. Once approved, obtain your OpenAI API Key from the OpenAI ACP dashboard
3. Get your Stripe API keys from https://dashboard.stripe.com/apikeys
4. Enter all API keys in the plugin settings page
5. Submit your product feed URL to OpenAI for indexing

== Frequently Asked Questions ==

= How do I install this plugin? =

The easiest way is to install directly from WordPress.org:
1. Go to Plugins > Add New in your WordPress admin
2. Search for "Instant Checkout ACP"
3. Click Install Now, then Activate
4. Go to WooCommerce > AI Checkout to configure

= What is the Agentic Commerce Protocol (ACP)? =

ACP is an open standard that allows AI agents, like ChatGPT, to interact with e-commerce stores in a standardized way. This enables features like "Buy it in ChatGPT."

= Do I need to apply for OpenAI ACP approval? =

Yes, you need to apply at https://openai.com/index/buy-it-in-chatgpt/ and get approved before your products can appear in ChatGPT.

= Do I need a Stripe account? =

Yes, a Stripe account is required for payment processing.

= Where are the plugin settings? =

After activation, go to WooCommerce > AI Checkout in your WordPress admin menu.

= Can I control which products appear in ChatGPT? =

Yes! Version 1.1.0 adds per-product controls. Edit any product and use the "ChatGPT Checkout" meta box to enable/disable it. You can also use bulk actions on the product list to enable/disable multiple products at once.

= How do I test the integration safely? =

Enable Test Mode in the plugin settings (WooCommerce > AI Checkout). All orders created while test mode is active will be marked as test orders with clear visual indicators.

= Where can I find my product feed? =

Your product feed is available at `https://yourdomain.com/wp-json/icvaac/v1/product-feed`.

= Is this plugin free? =

Yes, the plugin is completely free and open source under GPLv2 license.

= Does this work with any store? =

Yes, as long as you meet the minimum requirements (WordPress 5.0+, WooCommerce 5.0+, PHP 7.4+).

= Who develops this plugin? =

This plugin is developed and maintained by Web Vijayi (founded by Lokesh Motwani), independent of OpenAI, Stripe, or WooCommerce.

== Screenshots ==

1.  The plugin settings page in the WordPress dashboard.
2.  An example of an order placed through ACP, as seen in the WooCommerce order details.

== Changelog ==

= 1.1.0 =
*   **NEW:** Per-product ChatGPT checkout control - enable/disable individual products
*   **NEW:** Test mode for safe testing before going live
*   **NEW:** Bulk actions to enable/disable multiple products at once
*   **NEW:** OpenAI bots setup instructions panel with robots.txt rules
*   **IMPROVED:** Settings moved to WooCommerce menu for better organization (WooCommerce > AI Checkout)
*   **IMPROVED:** Test orders are clearly marked with visual indicators
*   **IMPROVED:** Product feed now respects per-product settings
*   **IMPROVED:** Automatic cache invalidation on bulk product updates
*   Backward compatible: existing products remain enabled by default

= 1.0.0 =
*   Initial release
*   Full Agentic Commerce Protocol (ACP) implementation
*   REST API endpoints for ChatGPT integration
*   Automatic product feed generation
*   Stripe payment processing with PCI-compliant tokenization
*   WooCommerce dependency checking with proper error handling
*   Modern admin interface with setup wizard
*   Support for WordPress 5.0+ and WooCommerce 5.0+
*   Cross-platform compatible WordPress.org package

== Upgrade Notice ==

= 1.1.0 =
Major update! New per-product controls, test mode, bulk actions, and improved admin interface. Settings moved to WooCommerce menu. Fully backward compatible.

= 1.0.0 =
Initial release. Enable "Buy it in ChatGPT" for your WooCommerce store!

== About Web Vijayi ==

Web Vijayi specializes in innovative e-commerce solutions. Founded by Lokesh Motwani, we create tools that help online store owners succeed in the evolving digital commerce landscape.

Website: https://webvijayi.com
