# WooCommerce ACP Instant Checkout

## Download

You can download the latest installable version of this plugin from the [Releases page](https://github.com/lmotwani/woocommerce-acp-instant-checkout/releases).

**Contributors:** [Lokesh Motwani](https://lokeshmotwani.com)
**Tags:** woocommerce, openai, chatgpt, acp, agentic-commerce, checkout
**Requires at least:** 5.0
**Tested up to:** 6.4
**Stable tag:** 1.0.0
**Requires PHP:** 7.4
**WC requires at least:** 5.0
**WC tested up to:** 8.0
**License:** [Apache-2.0](https://www.apache.org/licenses/LICENSE-2.0)

Enable OpenAI's Agentic Commerce Protocol (ACP) for any WooCommerce store, allowing "Buy it in ChatGPT" functionality.

## Description

This plugin enables any WooCommerce-powered website to participate in **OpenAI’s Agentic Commerce Protocol (ACP)**—the open standard that powers **“Buy it in ChatGPT” and other AI agent-driven checkouts**.

### Key Features

*   **Turns Your Store Into an AI-Ready Merchant:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Connects Directly With ChatGPT:** Your products can be surfaced in ChatGPT search results, and users can complete purchases natively within the chat experience.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.

## Installation

### User Installation

1.  Download the latest `.zip` file from the Releases page.
2.  Upload the `.zip` file through the WordPress plugins screen.
3.  Activate the plugin.
4.  Go to **WooCommerce > Settings > ACP/ChatGPT** to configure the plugin.
5.  Apply for OpenAI ACP approval at https://openai.com/index/buy-it-in-chatgpt/.
6.  (After approval) Copy your ACP OpenAI API Key from the OpenAI ACP dashboard.
7.  Enable the ACP API and paste your OpenAI API Key in the settings above.
8.  Enter your Stripe API keys for payment processing.
9.  Submit your Product Feed URL to OpenAI for indexing if required or requested.
10. Test and complete your integration using OpenAI's developer tools or ChatGPT.

### Developer Setup

1.  Clone this repository into your `/wp-content/plugins/` directory.
2.  Run `composer install` to download the Stripe PHP SDK.
3.  Activate the plugin through the 'Plugins' screen in WordPress.
4.  Go to **WooCommerce > Settings > ACP/ChatGPT** to configure the plugin.

## Development

To contribute to this plugin, please fork the repository and submit a pull request.

### Contribution Guidelines

*   Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).
*   Ensure all new code is well-documented.
*   Test your changes thoroughly before submitting a pull request.