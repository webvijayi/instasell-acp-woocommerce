# Production Ready Checklist

## ✅ Completed Fixes

All WordPress coding standards issues have been resolved:

### Code Quality Issues - FIXED ✅
- [x] Slow database query warnings (3) - Added phpcs:ignore with justification
- [x] Development function usage (1) - Wrapped error_log in WP_DEBUG conditional
- [x] Missing translator comments (5) - Added all required translator comments
- [x] Text domain consistency (49) - All updated to correct domain
- [x] Security escaping (38) - All outputs properly escaped
- [x] Input validation - Added isset() checks and wp_unslash()
- [x] WordPress best practices - Using wp_strip_all_tags() instead of strip_tags()

### Files Modified
1. `includes/class-wcacp-product-feed.php` - Fixed meta_query warning
2. `includes/class-wcacp-checkout-session.php` - Fixed meta_key/meta_value warnings + bug fix
3. `woocommerce-acp-instant-checkout.php` - Already compliant
4. `includes/class-wcacp-admin.php` - Previously fixed
5. `includes/class-wcacp-post-types.php` - Previously fixed

## ⚠️ Manual Action Required

### Plugin Slug Rename (CRITICAL)

**Current Status:** The plugin slug violates WordPress.org trademark policy

**What needs to be done:**
1. Rename directory: `woocommerce-acp-instant-checkout` → `acp-instant-checkout-for-woocommerce`
2. Rename main file: `woocommerce-acp-instant-checkout.php` → `acp-instant-checkout-for-woocommerce.php`
3. Update text domain in all files
4. Update build scripts

**Detailed Instructions:** See `SLUG-RENAME-REQUIRED.md`

## Verification Steps

### 1. Run PHPCS Check
```bash
phpcs --standard=WordPress woocommerce-acp-instant-checkout/
```

**Expected Result:** No errors, only the trademark warning about slug name

### 2. After Slug Rename
```bash
phpcs --standard=WordPress acp-instant-checkout-for-woocommerce/
```

**Expected Result:** Clean - no errors or warnings

### 3. Test Plugin Functionality
- [ ] Activate plugin in WordPress
- [ ] Configure ACP settings
- [ ] Test product feed endpoint: `/wp-json/wcacp/v1/product-feed`
- [ ] Test checkout session creation
- [ ] Verify all admin settings work

### 4. Build Distribution Package
```bash
# Linux/Mac
./build.sh

# Windows
build.bat
```

### 5. Final Checks Before WordPress.org Submission
- [ ] Plugin slug renamed to `acp-instant-checkout-for-woocommerce`
- [ ] All PHPCS checks pass with zero warnings
- [ ] Plugin tested on fresh WordPress install
- [ ] readme.txt properly formatted
- [ ] All dependencies included in vendor/
- [ ] No development files in distribution
- [ ] License is GPL-2.0-or-later
- [ ] Tested up to current WordPress version (6.8)

## WordPress.org Submission

Once all checklist items are complete:

1. **Create Account:** https://login.wordpress.org/register
2. **Submit Plugin:** https://wordpress.org/plugins/developers/add/
3. **Upload:** `acp-instant-checkout-for-woocommerce.zip`
4. **Wait:** Review typically takes 1-14 days

## Support Resources

- **WordPress.org Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- **Trademark Policy:** https://wordpressfoundation.org/trademark-policy/
- **Plugin Handbook:** https://developer.wordpress.org/plugins/
- **SVN Guide:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/

## Current Status

🟡 **ALMOST READY** - One manual step remaining (slug rename)

After completing the slug rename, the plugin will be:
✅ **READY FOR WORDPRESS.ORG SUBMISSION**
