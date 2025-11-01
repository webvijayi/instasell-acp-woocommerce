=== InstaSell with ACP for WooCommerce ===
Contributors: webvijayi
Tags: chatgpt, acp, checkout, ai-commerce, stripe, selling
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.3
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enable the Agentic Commerce Protocol (ACP) allowing "Buy it in ChatGPT" functionality - help WooCommerce store owners sell more with AI-powered checkout.

== Description ==

InstaSell enables any WooCommerce-powered website to participate in the **Agentic Commerce Protocol (ACP)**—the open standard that powers **"Buy it in ChatGPT" and other AI agent-driven checkouts**.

**Developed by Web Vijayi** to help WooCommerce store owners tap into the future of AI-powered commerce.

= Key Features =

*   **AI-Ready Sales Channel:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Sell Directly via ChatGPT:** Your products can be surfaced in ChatGPT search results, and customers can complete purchases natively within the chat experience.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.
*   **Easy Integration:** Works seamlessly with your existing store setup with minimal configuration required.

= About ACP =

The Agentic Commerce Protocol (ACP) is an open standard developed to enable AI agents to interact with e-commerce platforms. This plugin implements the ACP specification to allow your WooCommerce store to work with ChatGPT and other ACP-compatible AI agents.

= Why InstaSell? =

InstaSell focuses on helping store owners **sell more** by tapping into emerging AI-powered shopping channels. As AI agents become the new way customers discover and purchase products, InstaSell ensures your WooCommerce store is ready.

== Installation ==

1.  Upload the plugin files to the `/wp-content/plugins/instasell-acp-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Go to **Settings > InstaSell ACP** to configure the plugin.

== Configuration ==

After installation:

1. Apply for ACP approval at https://openai.com/index/buy-it-in-chatgpt/
2. Once approved, obtain your OpenAI API Key from the OpenAI ACP dashboard
3. Get your Stripe API keys from https://dashboard.stripe.com/apikeys
4. Enter all API keys in the plugin settings page
5. Submit your product feed URL to OpenAI for indexing

== Frequently Asked Questions ==

= What is the Agentic Commerce Protocol (ACP)? =

ACP is an open standard that allows AI agents, like ChatGPT, to interact with e-commerce stores in a standardized way. This enables features like "Buy it in ChatGPT."

= Do I need to apply for OpenAI ACP approval? =

Yes, you need to apply at https://openai.com/index/buy-it-in-chatgpt/ and get approved before your products can appear in ChatGPT.

= Do I need a Stripe account? =

Yes, a Stripe account is required for payment processing.

= Where can I find my product feed? =

Your product feed is available at `https://yourdomain.com/wp-json/instsl/v1/product-feed`.

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

= 1.0.3 =
*   **CRITICAL FIX:** Fixed "Plugin file does not exist" error on activation
*   Improved WooCommerce dependency check to use wp_die() instead of deactivate_plugins()
*   Now shows proper error page with "Back" link if WooCommerce is missing
*   Prevents plugin activation confusion and WordPress state issues

= 1.0.2 =
*   **CRITICAL FIX:** Added WooCommerce dependency checking with admin notices
*   Plugin now displays clear error message if WooCommerce is not installed
*   Prevents plugin activation if WooCommerce is missing (backward compatibility for WordPress < 6.5)
*   Shows admin notice with installation link if WooCommerce is deactivated
*   Prevents fatal errors on sites without WooCommerce

= 1.0.1 =
*   Updated plugin name to comply with WordPress.org guidelines (InstaSell branding)
*   Updated Stripe PHP SDK to version 18.0
*   Added "Requires Plugins" header for WooCommerce dependency
*   Improved prefix naming to avoid conflicts (INSTSL)
*   Updated all REST API endpoints to use instsl/v1 namespace
*   Enhanced documentation and setup instructions
*   Clarified authorship (Web Vijayi)

= 1.0.0 =
*   Initial release.

== Upgrade Notice ==

= 1.0.3 =
CRITICAL! Fixes "Plugin file does not exist" error. If you experienced activation issues, this update resolves them. Update immediately.

= 1.0.2 =
Critical bug fix! Adds WooCommerce dependency checking to prevent fatal errors. Highly recommended update for all users.

= 1.0.1 =
Important update for WordPress.org compliance. Updates naming conventions and Stripe library. Please reconfigure your API keys after updating.

== About Web Vijayi ==

Web Vijayi specializes in innovative e-commerce solutions. Founded by Lokesh Motwani, we create tools that help online store owners succeed in the evolving digital commerce landscape.

Website: https://webvijayi.com
