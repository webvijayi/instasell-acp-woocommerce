# Instant Checkout via ACP Agentic Commerce for WooCommerce

> **📢 WordPress.org Status:** This plugin is currently **under review** for submission to the WordPress.org Plugin Directory. Once approved, it will be available for one-click installation directly from your WordPress admin.

## Download

Download the latest installable version from the [Releases page](https://github.com/webvijayi/instant-checkout-via-acp-agentic-commerce-for-woocommerce/releases).

**Contributors:** Web Vijayi
**Tags:** woocommerce, chatgpt, acp, agentic-commerce, checkout, ai
**Requires at least:** 5.0
**Tested up to:** 6.8
**Stable tag:** 1.0.0
**Requires PHP:** 7.4
**WC requires at least:** 5.0
**WC tested up to:** 8.0
**License:** [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

Enable the Agentic Commerce Protocol (ACP) for any WooCommerce store, allowing "Buy it in ChatGPT" functionality.

## Description

This plugin enables any WooCommerce-powered website to participate in the **Agentic Commerce Protocol (ACP)**—the open standard that powers **"Buy it in ChatGPT" and other AI agent-driven checkouts**.

### Key Features

*   **Turns Your Store Into an AI-Ready Merchant:** Creates all the REST API endpoints required by the ACP, generates a machine-readable product feed, and handles the entire checkout process.
*   **Connects Directly With ChatGPT:** Your products can be surfaced in ChatGPT search results, and users can complete purchases natively within the chat experience.
*   **Secure, PCI-Compliant Commerce:** Utilizes Stripe's secure tokenization for payments, ensuring that sensitive data is handled safely.
*   **Easy Integration:** Works seamlessly with your existing WooCommerce setup with minimal configuration.

### About ACP

The Agentic Commerce Protocol (ACP) is an open standard that allows AI agents to interact with e-commerce platforms. This plugin is an independent implementation of the ACP specification, developed by Web Vijayi (founded by Lokesh Motwani), and is not officially affiliated with OpenAI, Stripe, or WooCommerce.

## Installation

### For End Users (No Composer Required!)

The plugin comes pre-packaged with all dependencies. To install:

1.  Download the latest `.zip` file from the [Releases page](https://github.com/webvijayi/instant-checkout-via-acp-agentic-commerce-for-woocommerce/releases)
2.  In WordPress admin, go to **Plugins > Add New > Upload Plugin**
3.  Choose the `.zip` file and click **Install Now**
4.  Click **Activate Plugin**
5.  Go to **Settings > Instant Checkout ACP ACP** to configure the plugin
6.  Apply for OpenAI ACP approval at https://openai.com/index/buy-it-in-chatgpt/
7.  (After approval) Copy your ACP OpenAI API Key from the OpenAI ACP dashboard
8.  Enable the ACP API and paste your OpenAI API Key in the settings
9.  Enter your Stripe API keys for payment processing
10. Submit your Product Feed URL to OpenAI for indexing
11. Test your integration using OpenAI's developer tools or ChatGPT

### Developer Setup

1.  Clone this repository into your `/wp-content/plugins/` directory.
2.  Run `composer install` to download dependencies (Stripe PHP SDK v18.0+, JSON Schema validator).
3.  Activate the plugin through the 'Plugins' screen in WordPress.
4.  Go to **Settings > Instant Checkout ACP ACP** to configure the plugin.

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
- Update Stripe library to latest version (v18.0+)
- Create an optimized autoloader
- Generate `instant-checkout-via-acp-agentic-commerce-for-woocommerce.zip` with all necessary files including the `vendor/` directory

The generated zip can be uploaded directly to WordPress without requiring users to run Composer.

## Configuration

After installation, configure the plugin in **Settings > Instant Checkout ACP ACP**:

1. **Enable ACP API** - Toggle to activate the ACP endpoints
2. **OpenAI API Key** - Your API key from the OpenAI ACP dashboard (after approval)
3. **Stripe Publishable Key** - Your Stripe publishable key
4. **Stripe Secret Key** - Your Stripe secret key
5. **Stripe Webhook Secret** - Your webhook secret from Stripe dashboard

### Product Feed URL

Your product feed will be available at:
```
https://yourdomain.com/wp-json/icvaac/v1/product-feed
```

Submit this URL to OpenAI for indexing after approval.

## Development

To contribute to this plugin, please fork the repository and submit a pull request.

### Contribution Guidelines

*   Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).
*   Use the `ICVAAC_` prefix for all classes and `icvaac_` for all functions and options
*   Ensure all new code is well-documented.
*   Test your changes thoroughly before submitting a pull request.

### Plugin Structure

```
instant-checkout-via-acp-agentic-commerce-for-woocommerce.php - Main plugin file
includes/
  ├── class-icvaac-admin.php          - Admin settings interface
  ├── class-icvaac-api-endpoints.php  - REST API endpoints
  ├── class-icvaac-checkout-session.php - Checkout session handler
  ├── class-icvaac-product-feed.php   - Product feed generator
  └── class-icvaac-post-types.php     - Custom post types
assets/
  ├── css/                            - Stylesheets
  └── js/                             - JavaScript files
schemas/                              - JSON validation schemas
vendor/                               - Composer dependencies (bundled in distribution)
```

## Resources

*   **Agentic Commerce Protocol**: https://agenticcommerce.dev/
*   **ACP Specification**: https://github.com/agentic-commerce-protocol/agentic-commerce-protocol
*   **OpenAI ACP Documentation**: https://developers.openai.com/commerce
*   **Stripe ACP Integration**: https://docs.stripe.com/agentic-commerce
*   **Apply for ChatGPT Integration**: https://openai.com/index/buy-it-in-chatgpt/

## Changelog

### 1.0.1 - 2025-01-31
*   Updated plugin name to comply with WordPress.org guidelines
*   Updated Stripe PHP SDK to version 18.0
*   Added "Requires Plugins" header for WooCommerce dependency
*   Improved prefix naming to avoid conflicts (ICVAAC)
*   Updated all REST API endpoints to use icvaac/v1 namespace
*   Enhanced documentation and setup instructions
*   Fixed ownership metadata

### 1.0.0 - 2025-01-01
*   Initial release

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/webvijayi/instant-checkout-via-acp-agentic-commerce-for-woocommerce).

## About Web Vijayi

**Web Vijayi** specializes in innovative e-commerce solutions that help online store owners succeed in the evolving digital commerce landscape.

**Founded by:** Lokesh Motwani
**Website:** [https://webvijayi.com](https://webvijayi.com)

Instant Checkout ACP is developed to help WooCommerce merchants tap into emerging AI-powered shopping channels like ChatGPT, ensuring stores are ready for the future of commerce.

## License

This plugin is licensed under the GPLv2 or later.
