# Manual Build Instructions

## Current Situation

Your system has an SSL certificate issue preventing Composer from downloading dependencies. Here's how to fix it and create the release zip.

## Option 1: Fix SSL Issue (Recommended)

### For Windows with PHP

1. **Download cacert.pem**
   - Go to: https://curl.se/ca/cacert.pem
   - Save as `C:\php\cacert.pem` (or wherever your PHP is installed)

2. **Update php.ini**
   ```ini
   ; Find this line and uncomment it:
   curl.cainfo = "C:\php\cacert.pem"
   
   ; Also add:
   openssl.cafile = "C:\php\cacert.pem"
   ```

3. **Restart and try again**
   ```bash
   php composer.phar install --no-dev --optimize-autoloader
   ```

## Option 2: Use Another Machine

If you have access to another computer (or use a cloud service):

1. Clone the repo
2. Run `composer install --no-dev --optimize-autoloader`
3. The `vendor/` directory will be created
4. Copy the entire `vendor/` directory back to your machine

## Option 3: Download Dependencies Manually

### Download Stripe PHP SDK
1. Go to: https://github.com/stripe/stripe-php/releases
2. Download latest release (v10.x)
3. Extract to `vendor/stripe/stripe-php/`

### Download JSON Schema
1. Go to: https://github.com/justinrainbow/json-schema/releases
2. Download latest v5.x release
3. Extract to `vendor/justinrainbow/json-schema/`

### Create Autoloader
This is complex - Option 1 or 2 is better.

## After Dependencies Are Installed

Once you have the `vendor/` directory with dependencies:

### Windows
```bash
build.bat
```

### Mac/Linux
```bash
chmod +x build.sh
./build.sh
```

This will create: `woocommerce-acp-instant-checkout.zip`

## Creating GitHub Release

1. **Go to GitHub**
   - https://github.com/lmotwani/woocommerce-acp-instant-checkout/releases

2. **Click "Draft a new release"**

3. **Fill in details:**
   - Tag: `v1.0.0`
   - Title: `WooCommerce ACP Instant Checkout v1.0.0`
   - Description:
     ```markdown
     # WooCommerce ACP Instant Checkout v1.0.0
     
     Enable "Buy it in ChatGPT" functionality for your WooCommerce store.
     
     ## Installation
     1. Download the zip file below
     2. WordPress Admin → Plugins → Add New → Upload Plugin
     3. Choose the zip file and install
     4. Activate and configure at WooCommerce → Settings → ACP/ChatGPT
     
     ## Requirements
     - WordPress 5.0+
     - WooCommerce 5.0+
     - PHP 7.4+
     - SSL certificate (HTTPS)
     
     ## Resources
     - Apply for OpenAI ACP: https://openai.com/index/buy-it-in-chatgpt/
     - ACP Documentation: https://agenticcommerce.dev/
     ```

4. **Upload the zip file**
   - Drag and drop `woocommerce-acp-instant-checkout.zip`

5. **Publish release**

## WordPress.org Submission

After creating the GitHub release:

1. **Prepare readme.txt**
   - Already exists in the repo
   - Follows WordPress.org format

2. **Create screenshots** (optional but recommended)
   - Take screenshots of:
     - Settings page
     - Product feed
     - ChatGPT integration
   - Save as `screenshot-1.png`, `screenshot-2.png`, etc.

3. **Submit to WordPress.org**
   - Go to: https://wordpress.org/plugins/developers/add/
   - Fill in the form
   - Upload the zip file
   - Wait for review (usually 1-2 weeks)

## Need Help?

If you're stuck on the SSL issue, you can:
1. Use GitHub Codespaces (free cloud environment)
2. Use a different computer
3. Ask someone to build it for you
4. Fix the SSL certificate issue (Option 1 above)

The key is getting the `vendor/` directory with Stripe and JSON Schema libraries installed.
