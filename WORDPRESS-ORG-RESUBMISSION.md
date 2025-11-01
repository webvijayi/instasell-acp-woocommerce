# WordPress.org Resubmission Information

**Plugin Name:** InstaSell with ACP for WooCommerce
**Version:** 1.0.2
**Submission Type:** Resubmission with Compliance Updates

---

## RESUBMISSION NOTES

This is a **resubmission** of the plugin with the following compliance updates based on previous review feedback:

### Changes Made Since Last Review

1. **Plugin Name Updated** - Changed from "ACP Instant Checkout for WooCommerce" to "InstaSell with ACP for WooCommerce" to comply with trademark guidelines
2. **Unique Prefix** - All classes, functions, and options now use unique `INSTSL_` prefix (previously used prohibited `WooCommerce_ACP_` prefix)
3. **Stripe Library Updated** - Updated Stripe PHP SDK from v10.21.0 to **v18.0** (latest stable)
4. **Dependency Header Added** - Added `Requires Plugins: woocommerce` header for WordPress 6.5+ compatibility
5. **Authorship Clarified** - Clear attribution to Web Vijayi (webvijayi username) founded by Lokesh Motwani
6. **WooCommerce Dependency Checking Added (v1.0.2)** - Critical bug fix adding runtime WooCommerce dependency checking with admin notices

### Repository Information

- **GitHub Repository:** https://github.com/webvijayi/instasell-acp-woocommerce
- **Author:** Web Vijayi
- **WordPress.org Username:** webvijayi
- **Founder/Developer:** Lokesh Motwani

---

## 1. EXTERNAL SERVICES

### Stripe API (stripe.com)
- **Purpose:** Secure payment processing
- **When Used:** Only when user configures Stripe API keys and processes payments
- **Data Sent:** Payment information via Stripe's secure tokenization
- **Requirements:** Merchant's own Stripe account required
- **Privacy:** PCI-DSS compliant, no credit card data stored locally
- **Terms of Service:** https://stripe.com/legal
- **Privacy Policy:** https://stripe.com/privacy

### OpenAI ACP (openai.com)
- **Purpose:** ChatGPT "Buy it in ChatGPT" integration
- **When Used:** Only when user enables ACP API and configures OpenAI API key
- **Data Sent:** Product catalog (public WooCommerce product data only)
- **Requirements:** OpenAI ACP approval required (apply at https://openai.com/index/buy-it-in-chatgpt/)
- **Privacy:** Only publicly available product information is shared
- **Terms of Service:** https://openai.com/policies/terms-of-use
- **API Documentation:** https://platform.openai.com/docs/

### Important Data Privacy Notes

- ✅ **No data is sent to external services without explicit user configuration**
- ✅ **All API keys must be manually entered by site administrator**
- ✅ **Users must apply for and receive approval from OpenAI before integration works**
- ✅ **Payment processing is entirely optional and requires Stripe account**
- ✅ **Plugin can be installed but remains inactive until configured**

---

## 2. THIRD-PARTY LIBRARIES (BUNDLED)

All dependencies are **bundled in the plugin zip** for ease of installation. End users do NOT need to run Composer.

### Stripe PHP SDK
- **Version:** v18.0 (updated from v10.21.0)
- **License:** MIT License (GPL-compatible ✅)
- **Purpose:** Payment processing via Stripe API
- **Source:** https://github.com/stripe/stripe-php
- **Included in plugin:** Yes (vendor/stripe/stripe-php/)
- **Security:** Official Stripe library, regularly updated

### JSON Schema Validator
- **Version:** v5.2+
- **License:** MIT License (GPL-compatible ✅)
- **Purpose:** Validate ACP API requests against JSON schemas
- **Source:** https://github.com/justinrainbow/json-schema
- **Included in plugin:** Yes (vendor/justinrainbow/json-schema/)

### Composer Autoloader
- **Included in plugin:** Yes (vendor/autoload.php)
- **Purpose:** Load dependencies automatically

**All libraries are included in the distribution zip.** Users install the zip file directly - no Composer or command-line tools required.

---

## 3. DATA & PRIVACY

### Payment Data Handling
- ✅ Payment data is processed through **Stripe's secure API** (PCI-compliant)
- ✅ **No credit card data is stored** on the WordPress site
- ✅ **Stripe secure tokenization** used for all payment information
- ✅ Payment processing requires user's own Stripe account

### Checkout Sessions
- Stored as **custom post types** in WordPress database
- Contains: session ID, line items, totals, customer info
- **No sensitive payment data** stored locally
- Sessions expire and can be cleaned up

### Product Data
- Product catalog exposed via **REST API endpoint** (`/wp-json/instsl/v1/product-feed`)
- **Only public WooCommerce product data** is exposed (title, description, price, images)
- No customer data, order data, or private information exposed
- Endpoint requires OpenAI API key authentication

### User Data
- Plugin does **not collect any user data** for external purposes
- All data remains in WordPress database or is sent to configured services (Stripe/OpenAI) only
- Full compliance with WordPress.org privacy policies

---

## 4. REQUIREMENTS

### WordPress & WooCommerce
- **WordPress:** 5.0 or higher (tested up to 6.8)
- **WooCommerce:** 5.0 or higher (tested up to 8.0)
- **WooCommerce dependency enforced** via:
  - `Requires Plugins: woocommerce` header (WordPress 6.5+)
  - Runtime dependency checking with admin notices (all WordPress versions)
  - Automatic deactivation if WooCommerce is missing

### Server Requirements
- **PHP:** 7.4 or higher
- **SSL Certificate:** Required for production use (HTTPS)
- **cURL:** Required for API communication

### External Accounts (Optional, User-Configured)
- **Stripe Account:** Required only if using payment processing
- **OpenAI ACP Approval:** Required only if using ChatGPT integration
- Plugin functions without these but provides setup instructions

---

## 5. SECURITY & BEST PRACTICES

### API Key Security
- ✅ All API keys stored in WordPress options (wp_options table)
- ✅ Keys never exposed in frontend code
- ✅ REST API endpoints use permission callbacks
- ✅ OpenAI API key validated via headers

### Input Validation
- ✅ All user inputs sanitized using WordPress functions
- ✅ REST API requests validated against JSON schemas
- ✅ WooCommerce product data validation

### Prefix Compliance
- ✅ All classes prefixed: `INSTSL_`
- ✅ All functions prefixed: `instsl_`
- ✅ All options prefixed: `instsl_`
- ✅ REST namespace: `instsl/v1`
- ✅ Text domain: `instasell-acp-woocommerce`

### Direct File Access Prevention
- ✅ All PHP files include `if (!defined('ABSPATH')) { exit; }`

---

## 6. PLUGIN FUNCTIONALITY OVERVIEW

### Core Features
1. **Product Feed Generation** - Creates machine-readable product catalog for AI agents
2. **ACP REST API Endpoints** - Implements Agentic Commerce Protocol specification
3. **Checkout Session Management** - Handles AI-initiated checkout flows
4. **Stripe Payment Integration** - Processes payments securely via Stripe
5. **Settings Interface** - WordPress admin settings page for configuration

### User Workflow
1. Install plugin
2. Activate (requires WooCommerce)
3. Go to Settings > InstaSell ACP
4. Apply for OpenAI ACP approval (external process)
5. Configure API keys (OpenAI + Stripe)
6. Enable ACP API
7. Submit product feed to OpenAI
8. Products become available in ChatGPT

---

## 7. SUPPORT & DOCUMENTATION

- **Plugin URI:** https://github.com/webvijayi/instasell-acp-woocommerce
- **Support:** GitHub Issues
- **Documentation:** README.md included in plugin
- **Author:** Web Vijayi (https://webvijayi.com)
- **Founded by:** Lokesh Motwani

---

## 8. CHANGELOG (v1.0.0 → v1.0.2)

### v1.0.2 (Latest)
- **CRITICAL FIX:** Added WooCommerce dependency checking with admin notices
- Prevents plugin activation if WooCommerce is missing
- Shows clear error messages with installation instructions
- Backward compatibility for WordPress < 6.5

### v1.0.1 (Compliance Update)
- Renamed to "InstaSell with ACP for WooCommerce"
- Updated Stripe PHP SDK to v18.0
- Changed prefix to INSTSL_/instsl_
- Added "Requires Plugins" header
- Updated all REST API endpoints to instsl/v1

### v1.0.0 (Initial)
- Initial submission (rejected due to trademark/prefix issues)

---

## SUBMISSION CHECKLIST

- ✅ Plugin name compliant (InstaSell - unique brand)
- ✅ No trademark violations
- ✅ Unique prefix (INSTSL_/instsl_)
- ✅ WooCommerce dependency enforced
- ✅ Current libraries (Stripe v18.0)
- ✅ All dependencies bundled
- ✅ External services documented
- ✅ Privacy compliance
- ✅ GPL-compatible licenses
- ✅ Repository URL working (webvijayi/instasell-acp-woocommerce)
- ✅ Authorship clear (Web Vijayi / webvijayi)

---

**Ready for WordPress.org approval! 🚀**
