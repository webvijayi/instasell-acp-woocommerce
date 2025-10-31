# Plugin Slug Rename Required for WordPress.org Compliance

## Issue

The current plugin slug `woocommerce-acp-instant-checkout` violates WordPress.org trademark policy because it contains "woocommerce" but does NOT end with "-for-woocommerce".

According to WordPress.org policy:
> The plugin slug - "woocommerce-acp-instant-checkout" - contains the restricted term "woocommerce" which cannot be used within in your plugin slug, unless your plugin slug ends with "for woocommerce". The term must still not appear anywhere else in your plugin slug.

## Required Changes

### 1. Rename Plugin Directory
**Current:** `woocommerce-acp-instant-checkout/`
**Required:** `acp-instant-checkout-for-woocommerce/`

### 2. Rename Main Plugin File
**Current:** `woocommerce-acp-instant-checkout.php`
**Required:** `acp-instant-checkout-for-woocommerce.php`

### 3. Update Text Domain
**Current:** `Text Domain: woocommerce-acp-instant-checkout`
**Required:** `Text Domain: acp-instant-checkout-for-woocommerce`

## Manual Steps Required

Since directory and file renaming cannot be done automatically, please follow these steps:

### Step 1: Rename Directory
```bash
# Navigate to your plugins directory
cd wp-content/plugins/

# Rename the plugin directory
mv woocommerce-acp-instant-checkout acp-instant-checkout-for-woocommerce
```

### Step 2: Rename Main Plugin File
```bash
cd acp-instant-checkout-for-woocommerce/
mv woocommerce-acp-instant-checkout.php acp-instant-checkout-for-woocommerce.php
```

### Step 3: Update Text Domain in Main File
Edit `acp-instant-checkout-for-woocommerce.php` and change:
```php
Text Domain: woocommerce-acp-instant-checkout
```
to:
```php
Text Domain: acp-instant-checkout-for-woocommerce
```

### Step 4: Update All Text Domain References
Find and replace all instances of `'woocommerce-acp-instant-checkout'` with `'acp-instant-checkout-for-woocommerce'` in:
- `includes/class-wcacp-admin.php`
- `includes/class-wcacp-post-types.php`
- Any other files using translation functions

### Step 5: Update Build Scripts
Update these files to use the new slug:
- `build.sh` (if exists)
- `build.bat` (if exists)
- `.distignore` (if exists)
- `composer.json` (if exists)

### Step 6: Update readme.txt
No changes needed - the plugin name "ACP Instant Checkout for WooCommerce" is already compliant.

### Step 7: Update Repository
If using version control:
```bash
git mv woocommerce-acp-instant-checkout acp-instant-checkout-for-woocommerce
git commit -m "Rename plugin slug to comply with WordPress.org trademark policy"
```

## Why This Matters

WordPress.org will reject the plugin submission if the slug violates trademark policy. The slug must:
- ✅ End with "-for-woocommerce" if it contains "woocommerce"
- ✅ Not have "woocommerce" at the beginning or middle

**Current slug:** `woocommerce-acp-instant-checkout` ❌
**Compliant slug:** `acp-instant-checkout-for-woocommerce` ✅

## After Renaming

Once you've completed the manual rename:
1. Reactivate the plugin in WordPress (it will appear as a new plugin)
2. Reconfigure any settings if needed
3. Run PHPCS to verify all changes: `phpcs --standard=WordPress acp-instant-checkout-for-woocommerce/`
4. Test all functionality
5. Create a new build zip with the correct slug name
6. Submit to WordPress.org

## References

- [WordPress.org Plugin Guidelines - Trademarks](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#1-plugins-must-be-compatible-with-the-gnu-general-public-license)
- [WordPress.org Trademark Policy](https://wordpressfoundation.org/trademark-policy/)
