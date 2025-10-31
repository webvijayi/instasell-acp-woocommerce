# Pre-Push Checklist

Use this checklist before pushing changes to GitHub.

## Code Quality

- [x] No PHP syntax errors
- [x] All classes have proper docblocks
- [x] Direct access prevention in all PHP files (`if (!defined('ABSPATH')) exit;`)
- [x] WordPress coding standards followed
- [x] All methods referenced are implemented

## Functionality

- [x] Composer autoloader properly loaded
- [x] All ACP endpoints defined (create, get, update, complete, cancel)
- [x] Product feed endpoint implemented
- [x] Checkout session handling complete
- [x] Payment processing with Stripe integrated
- [x] Tax calculation implemented
- [x] Fulfillment options logic added

## Documentation

- [x] README.md updated with installation instructions
- [x] INSTALL.md created for end users
- [x] BUILD-GUIDE.md created for developers
- [x] QUICKSTART.md created for quick reference
- [x] RELEASE-CHECKLIST.md created for releases
- [x] All URLs verified and correct
- [x] Official ACP resources linked

## Build System

- [x] build.sh created (Linux/Mac)
- [x] build.bat created (Windows)
- [x] .distignore created
- [x] .gitattributes configured
- [x] build-exclude.txt created
- [x] composer.json has build script

## Distribution Ready

- [x] Plugin works without requiring users to run Composer
- [x] vendor/ directory will be included in distribution zip
- [x] All dependencies bundled
- [x] No development files in distribution

## Git

- [x] All new files added to git
- [x] Meaningful commit message prepared
- [x] No sensitive data in commits
- [x] .gitignore properly configured

## URLs Verified

- [x] https://agenticcommerce.dev/ - ACP official site
- [x] https://github.com/agentic-commerce-protocol/agentic-commerce-protocol - ACP spec
- [x] https://openai.com/index/buy-it-in-chatgpt/ - Apply for ChatGPT
- [x] https://developers.openai.com/commerce - OpenAI ACP docs
- [x] https://docs.stripe.com/agentic-commerce - Stripe ACP docs
- [x] https://developer.wordpress.org/ - WordPress docs
- [x] https://developer.woocommerce.com/docs/ - WooCommerce docs

## Ready to Push

All items checked! Ready to commit and push to GitHub.
