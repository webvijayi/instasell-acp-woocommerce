# Build Guide for Developers

This guide explains how to build a distributable version of the plugin that includes all dependencies.

## Why Build?

The plugin requires PHP libraries (Stripe SDK, JSON Schema validator) that are managed by Composer. End users shouldn't need to install Composer, so we bundle these dependencies in the distribution zip.

## Prerequisites

You only need Composer installed on your development machine:

### Installing Composer (Windows)

1. Download from https://getcomposer.org/download/
2. Run the installer
3. Restart your terminal/PowerShell

### Installing Composer (Mac/Linux)

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## Building the Plugin

### Option 1: Using Build Scripts (Recommended)

**Windows:**
```cmd
build.bat
```

**Linux/Mac:**
```bash
chmod +x build.sh
./build.sh
```

This creates `woocommerce-acp-instant-checkout.zip` ready for distribution.

### Option 2: Manual Build

If the build scripts don't work, follow these steps:

1. **Install production dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **Create a zip with these files/folders:**
   - `woocommerce-acp-instant-checkout.php` (main file)
   - `includes/` (all PHP classes)
   - `assets/` (CSS/JS files)
   - `schemas/` (JSON schemas)
   - `vendor/` (Composer dependencies - IMPORTANT!)
   - `README.md`
   - `readme.txt`
   - `INSTALL.md`

3. **Exclude these from the zip:**
   - `.git/`
   - `composer.json`
   - `composer.lock`
   - `build.sh`, `build.bat`
   - `tests/`
   - Development files

## What Gets Included

The `vendor/` directory contains:
- `stripe/stripe-php` - Stripe payment processing
- `justinrainbow/json-schema` - JSON validation
- Composer's autoloader

This is approximately 2-3 MB and allows the plugin to work immediately after installation.

## Testing the Build

1. Extract the zip to a test WordPress installation
2. Activate the plugin
3. Check that no errors appear
4. Verify settings page loads at **WooCommerce > Settings > ACP/ChatGPT**

## Distribution

The generated zip file can be:
- Uploaded directly to WordPress via **Plugins > Add New > Upload**
- Distributed via GitHub Releases
- Submitted to WordPress.org plugin directory (requires approval)

## Troubleshooting

**"Class 'Stripe\Stripe' not found"**
- The vendor directory wasn't included in the zip
- Rebuild with `composer install --no-dev` first

**"Composer not found"**
- Install Composer from https://getcomposer.org/

**Build script fails**
- Try the manual build steps above
- Ensure you have write permissions in the directory
