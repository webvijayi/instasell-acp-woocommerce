# WooCommerce (OpenAI - Buy in ChatGPT) ACP Instant Checkout Plugin

## Download

Download the latest installable version from the [Releases page](https://github.com/lmotwani/woocommerce-acp-instant-checkout/releases).

**Contributors:** [Lokesh Motwani](https://lokeshmotwani.com)
**Tags:** woocommerce, openai, chatgpt, acp, agentic-commerce, checkout
**Requires at least:** 5.0
**Tested up to:** 6.4
**Stable tag:** 1.0.0
**Requires PHP:** 7.4
**WC requires at least:** 5.0
**WC tested up to:** 8.0
**License:** [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

Enable OpenAI's Agentic Commerce Protocol (ACP) for any WooCommerce store, allowing "Buy it in ChatGPT" functionality.

## Description

This plugin enables any WooCommerce-powered website to participate in **OpenAI’s Agentic Commerce Protocol (ACP)**—the open standard that powers **“Buy it in ChatGPT” and other AI agent-driven checkouts**.

### Key Features

*   **Turns Your Store Into an AI-Ready Merchant:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Connects Directly With ChatGPT:** Your products can be surfaced in ChatGPT search results, and users can complete purchases natively within the chat experience.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.

## Installation

### For End Users (No Composer Required!)

The plugin comes pre-packaged with all dependencies. To install:

1.  Download the latest `.zip` file from the [Releases page](https://github.com/lmotwani/woocommerce-acp-instant-checkout/releases)
2.  In WordPress admin, go to **Plugins > Add New > Upload Plugin**
3.  Choose the `.zip` file and click **Install Now**
4.  Click **Activate Plugin**
5.  Go to **WooCommerce > Settings > ACP/ChatGPT** to configure the plugin
6.  Apply for OpenAI ACP approval at https://openai.com/index/buy-it-in-chatgpt/
7.  (After approval) Copy your ACP OpenAI API Key from the OpenAI ACP dashboard
8.  Enable the ACP API and paste your OpenAI API Key in the settings
9.  Enter your Stripe API keys for payment processing
10. Submit your Product Feed URL to OpenAI for indexing
11. Test your integration using OpenAI's developer tools or ChatGPT

### Developer Setup

1.  Clone this repository into your `/wp-content/plugins/` directory.
2.  Run `composer install` to download dependencies (Stripe PHP SDK, JSON Schema validator).
3.  Activate the plugin through the 'Plugins' screen in WordPress.
4.  Go to **WooCommerce > Settings > ACP/ChatGPT** to configure the plugin.

### Building for Distribution

To create a production-ready zip file for distribution:

**Linux/Mac:**
```bash
chmod +x build.sh
./build.sh
```

**Windows:**
```cmd
build.bat
```

This will:
- Install production dependencies (without dev packages)
- Create an optimized autoloader
- Generate `woocommerce-acp-instant-checkout.zip` with all necessary files including the `vendor/` directory

The generated zip can be uploaded directly to WordPress without requiring users to run Composer.

## Development

To contribute to this plugin, please fork the repository and submit a pull request.

### Contribution Guidelines

*   Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).
*   Ensure all new code is well-documented.
*   Test your changes thoroughly before submitting a pull request.

## Resources

*   **Agentic Commerce Protocol**: https://agenticcommerce.dev/
*   **ACP Specification**: https://github.com/agentic-commerce-protocol/agentic-commerce-protocol
*   **OpenAI ACP Documentation**: https://developers.openai.com/commerce
*   **Stripe ACP Integration**: https://docs.stripe.com/agentic-commerce
*   **Apply for ChatGPT Integration**: https://openai.com/index/buy-it-in-chatgpt/
