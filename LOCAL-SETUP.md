# Local Development Setup

This file is for developers working on the plugin locally. It's not tracked in Git.

## Quick Start

1. **Install Composer** (if not already installed)
   - Windows: https://getcomposer.org/download/
   - Mac: `brew install composer`
   - Linux: `curl -sS https://getcomposer.org/installer | php`

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Build distribution zip**
   ```bash
   # Windows
   build.bat
   
   # Mac/Linux
   chmod +x build.sh
   ./build.sh
   ```

## Local Files (Not in Git)

These files exist locally but are excluded from Git:

- `BUILD-GUIDE.md` - Detailed build instructions
- `INSTALL.md` - End user installation guide
- `QUICKSTART.md` - Quick reference
- `RELEASE-CHECKLIST.md` - Release process
- `PRE-PUSH-CHECKLIST.md` - Pre-push verification
- `build.sh` - Linux/Mac build script
- `build.bat` - Windows build script
- `build-exclude.txt` - Build exclusions
- `.distignore` - Distribution exclusions
- `vendor/` - Composer dependencies

## Why These Files Aren't in Git

- **Cleaner repository**: Focus on source code only
- **Smaller clone size**: No build artifacts or docs
- **Developer flexibility**: Each dev can customize build process
- **README.md is enough**: Main documentation in repo

## Building for Distribution

The build scripts will:
1. Install production dependencies (`composer install --no-dev`)
2. Create optimized autoloader
3. Bundle everything into `woocommerce-acp-instant-checkout.zip`
4. Include vendor/ directory for instant use

## What's in Git

Only essential source files:
- Plugin PHP files
- Assets (CSS/JS)
- JSON schemas
- README.md and readme.txt
- composer.json (for dependency management)

## Resources

- **Repository**: https://github.com/lmotwani/woocommerce-acp-instant-checkout
- **ACP Docs**: https://agenticcommerce.dev/
- **OpenAI ACP**: https://developers.openai.com/commerce
- **Stripe ACP**: https://docs.stripe.com/agentic-commerce
