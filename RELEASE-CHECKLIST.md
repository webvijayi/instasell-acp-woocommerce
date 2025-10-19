# Release Checklist

Use this checklist when preparing a new release of the plugin.

## Pre-Release

- [ ] Update version number in `woocommerce-acp-instant-checkout.php` header
- [ ] Update version in `readme.txt` (Stable tag)
- [ ] Update `README.md` if needed
- [ ] Update changelog in `readme.txt`
- [ ] Test plugin on clean WordPress installation
- [ ] Test with minimum required versions (WP 5.0, WC 5.0, PHP 7.4)
- [ ] Test with latest versions (WP 6.4+, WC 8.0+)
- [ ] Verify all API endpoints work
- [ ] Test Stripe integration
- [ ] Check for PHP errors/warnings

## Build Process

- [ ] Ensure Composer is installed
- [ ] Run build script:
  - Windows: `build.bat`
  - Mac/Linux: `./build.sh`
- [ ] Verify `woocommerce-acp-instant-checkout.zip` was created
- [ ] Check zip file size (should be 2-4 MB with vendor/)
- [ ] Extract and verify `vendor/` directory is included
- [ ] Test the zip on a clean WordPress installation

## Release

- [ ] Create GitHub release with version tag (e.g., `v1.0.0`)
- [ ] Upload the built zip file to GitHub release
- [ ] Copy changelog to release notes
- [ ] Mark as pre-release if beta/RC
- [ ] Publish release

## Post-Release

- [ ] Test download link from GitHub releases
- [ ] Update documentation if needed
- [ ] Announce release (if applicable)
- [ ] Monitor for issues

## WordPress.org Submission (Optional)

If submitting to WordPress.org plugin directory:

- [ ] Ensure readme.txt follows WordPress.org format
- [ ] Add screenshots to `/assets/` directory
- [ ] Create banner images (772×250px and 1544×500px)
- [ ] Submit via https://wordpress.org/plugins/developers/add/
- [ ] Respond to review feedback
- [ ] Use SVN to commit approved plugin

## Version Numbering

Follow semantic versioning (MAJOR.MINOR.PATCH):
- MAJOR: Breaking changes
- MINOR: New features, backward compatible
- PATCH: Bug fixes, backward compatible

Examples:
- `1.0.0` - Initial release
- `1.1.0` - New feature added
- `1.1.1` - Bug fix
- `2.0.0` - Breaking changes
