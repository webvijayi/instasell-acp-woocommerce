# WordPress.org Plugin Review Compliance Fixes

**Date:** January 31, 2025
**Plugin Version:** 1.0.1
**Review ID:** AUTOPREREVIEW ❗TRM-OWN acp-instant-checkout-for-woocommerce/webvijayi/31Oct25/T1

## Summary

This document outlines all changes made to comply with WordPress.org Plugin Directory guidelines following the automated review feedback.

---

## Issues Addressed

### ✅ 1. Plugin Name and Trademark Compliance

**Issue:** Plugin name "ACP Instant Checkout for WooCommerce" violated trademark guidelines by starting with "ACP" (Agentic Commerce Protocol), implying affiliation with OpenAI.

**Resolution:**
- **New Plugin Name:** "Webvijayi AI Checkout with ACP for WooCommerce"
- **New Slug:** `webvijayi-ai-checkout-acp-woocommerce`
- **Rationale:** Name now starts with unique brand identifier "Webvijayi" and uses "with ACP" to clarify no affiliation

**Files Updated:**
- `webvijayi-ai-checkout-acp-woocommerce.php` (new main file)
- `readme.txt` - Display name, contributors, slug references
- `README.md` - All branding and documentation
- `composer.json` - Package name

---

### ✅ 2. Ownership and Authorship Consistency

**Issue:** Mismatch between submitter username (webvijayi), contributor name (lokeshmotwani), and author metadata.

**Resolution:**
- **Primary Author:** Webvijayi (Lokesh Motwani)
- **WordPress.org Username:** webvijayi
- **Email:** webvijayi.com@gmail.com
- **Unified Identity:** All metadata now consistently references "Webvijayi (Lokesh Motwani)"

**Files Updated:**
- Plugin header: `Author: Webvijayi (Lokesh Motwani)`
- `readme.txt`: `Contributors: webvijayi`
- `composer.json`: Author field updated
- All documentation files

---

### ✅ 3. Prefix Naming Conflicts

**Issue:** Class name `WooCommerce_ACP_Instant_Checkout` used "WooCommerce" prefix, which is prohibited.

**Resolution:**
- **New Prefix:** `WVACP_` (Webvijayi Agentic Commerce Protocol)
- **Function Prefix:** `wvacp_`
- **Text Domain:** `webvijayi-ai-checkout-acp-woocommerce`
- **REST Namespace:** `wvacp/v1`

**All Classes Renamed:**
- `WooCommerce_ACP_Instant_Checkout` → `WVACP_AI_Checkout`
- `WCACP_Admin` → `WVACP_Admin`
- `WCACP_API_Endpoints` → `WVACP_API_Endpoints`
- `WCACP_Checkout_Session` → `WVACP_Checkout_Session`
- `WCACP_Product_Feed` → `WVACP_Product_Feed`
- `WCACP_Post_Types` → `WVACP_Post_Types`

**All Functions Renamed:**
- `woocommerce_acp_instant_checkout()` → `wvacp_ai_checkout()`
- All option names: `wcacp_*` → `wvacp_*`
- All action hooks: `wcacp_*` → `wvacp_*`

**Files Renamed:**
- `acp-instant-checkout-for-woocommerce.php` → `webvijayi-ai-checkout-acp-woocommerce.php`
- `includes/class-wcacp-*.php` → `includes/class-wvacp-*.php`

---

### ✅ 4. Stripe Library Version Update

**Issue:** Using Stripe PHP SDK version 10.21.0, which is outdated.

**Resolution:**
- **Updated to:** Stripe PHP SDK `^18.0` (latest stable)
- Updated in `composer.json` require section
- Better security and feature support

---

### ✅ 5. Plugin Dependencies Header

**Issue:** Missing "Requires Plugins" header for WooCommerce dependency.

**Resolution:**
Added header to main plugin file:
```php
* Requires Plugins: woocommerce
```

This ensures WordPress checks for WooCommerce before activation.

---

### ✅ 6. Plugin URI Issue

**Issue:** GitHub repository URL returned 404 error.

**Resolution:**
- **Old URL:** `https://github.com/lmotwani/acp-instant-checkout-for-woocommerce`
- **New URL:** `https://github.com/webvijayi/ai-checkout-acp-woocommerce`
- **Note:** Repository needs to be created/renamed on GitHub

---

## Files Structure

### New Files Created:
```
webvijayi-ai-checkout-acp-woocommerce.php (main plugin file)
includes/class-wvacp-admin.php
includes/class-wvacp-api-endpoints.php
includes/class-wvacp-checkout-session.php
includes/class-wvacp-product-feed.php
includes/class-wvacp-post-types.php
```

### Files Updated:
```
readme.txt (completely rewritten)
README.md (completely rewritten)
composer.json (name, author, Stripe version)
build.sh (new plugin slug)
build.bat (new plugin slug)
```

### Files to Delete (old versions):
```
acp-instant-checkout-for-woocommerce.php
includes/class-wcacp-*.php (all 5 files)
```

---

## API Endpoint Changes

### Old Endpoints:
- `GET /wp-json/wcacp/v1/product-feed`
- `POST /wp-json/wcacp/v1/checkout_sessions`
- etc.

### New Endpoints:
- `GET /wp-json/wvacp/v1/product-feed`
- `POST /wp-json/wvacp/v1/checkout_sessions`
- etc.

**⚠️ Breaking Change:** Existing integrations will need to update endpoint URLs.

---

## Database/Options Migration

### Old Option Names:
- `wcacp_enable_acp`
- `wcacp_openai_api_key`
- `wcacp_stripe_publishable_key`
- `wcacp_stripe_secret_key`
- etc.

### New Option Names:
- `wvacp_enable_acp`
- `wvacp_openai_api_key`
- `wvacp_stripe_publishable_key`
- `wvacp_stripe_secret_key`
- etc.

**⚠️ Note:** Users upgrading from 1.0.0 will need to reconfigure their settings.

---

## Build Process

### To Build Distribution Zip:

**Windows:**
```cmd
build.bat
```

**Linux/Mac:**
```bash
chmod +x build.sh
./build.sh
```

**Output:** `webvijayi-ai-checkout-acp-woocommerce.zip`

The build scripts now:
- Exclude old class files (`class-wcacp-*.php`)
- Exclude old main file (`acp-instant-checkout-for-woocommerce.php`)
- Include only new properly-prefixed files
- Bundle updated dependencies from vendor/

---

## WordPress.org Submission Checklist

### ☑️ Required Actions:

1. **Request New Slug Reservation**
   - Reply to WordPress.org review email
   - Request slug: `webvijayi-ai-checkout-acp-woocommerce`
   - Mention all files have been updated

2. **Update Plugin Files**
   - Upload new version via "Add your plugin" page while logged in as `webvijayi`
   - Build fresh zip using updated build scripts

3. **Reply to Review Email**
   - Keep response brief and direct
   - Confirm all issues addressed:
     ✅ Plugin name changed to "Webvijayi AI Checkout with ACP for WooCommerce"
     ✅ New slug requested: webvijayi-ai-checkout-acp-woocommerce
     ✅ Ownership clarified (webvijayi = Lokesh Motwani)
     ✅ All prefixes updated to WVACP_/wvacp_
     ✅ Stripe library updated to v18.0
     ✅ "Requires Plugins" header added
     ✅ No trademark violations

4. **Update GitHub Repository**
   - Rename repository to match new slug
   - Update all links and references
   - Create new release with v1.0.1

---

## Email Response Template

```
Hi WordPress Plugin Review Team,

I have addressed all the issues identified in the automated review:

1. **Plugin Name:** Changed to "Webvijayi AI Checkout with ACP for WooCommerce"
2. **New Slug Requested:** webvijayi-ai-checkout-acp-woocommerce
3. **Ownership:** I (webvijayi) am Lokesh Motwani - all author metadata now unified
4. **Prefixes:** All classes use WVACP_ prefix, all functions use wvacp_ prefix
5. **Stripe Library:** Updated from 10.21.0 to ^18.0
6. **Dependencies:** Added "Requires Plugins: woocommerce" header

The updated plugin has been uploaded. All code has been updated to use the new naming conventions and complies with WordPress.org guidelines.

Thank you for your review.

Best regards,
Lokesh Motwani (webvijayi)
```

---

## Technical Notes

### Constants Changed:
- `WCACP_PLUGIN_FILE` → `WVACP_PLUGIN_FILE`
- `WCACP_PLUGIN_DIR` → `WVACP_PLUGIN_DIR`
- `WCACP_PLUGIN_URL` → `WVACP_PLUGIN_URL`
- `WCACP_VERSION` → `WVACP_VERSION`

### Settings Page Location:
- Old: WooCommerce > Settings > ACP/ChatGPT
- New: Settings > Webvijayi AI Checkout

### Admin Menu Slug:
- Old: `wcacp-settings`
- New: `wvacp-settings`

---

## Version History

### 1.0.1 (Current - WordPress.org Compliance)
- Updated plugin name and slug
- Fixed all trademark issues
- Updated all prefixes
- Updated Stripe SDK
- Added dependency header
- Fixed ownership metadata

### 1.0.0 (Initial - Rejected)
- Original submission with non-compliant naming

---

## Maintenance Notes

### Going Forward:
- Always use `WVACP_` prefix for new classes
- Always use `wvacp_` prefix for new functions/options
- Text domain must be `webvijayi-ai-checkout-acp-woocommerce`
- Maintain clear "no affiliation" messaging in documentation
- Keep Stripe library updated regularly

### Before Next Release:
- Consider adding migration script for users upgrading from 1.0.0
- Update any external documentation/tutorials
- Notify any existing users about endpoint changes

---

**Document Status:** Complete
**Ready for Submission:** Yes
**Next Action:** Reply to WordPress.org review email and upload new version
