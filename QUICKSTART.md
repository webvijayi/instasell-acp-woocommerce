# Quick Start Guide

## I'm a Developer - How Do I Build This?

1. **Install Composer** (if you haven't already)
   - Windows: Download from https://getcomposer.org/download/
   - Mac: `brew install composer`
   - Linux: `curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer`

2. **Build the plugin**
   ```bash
   # Windows
   build.bat
   
   # Mac/Linux
   chmod +x build.sh
   ./build.sh
   ```

3. **Done!** You now have `woocommerce-acp-instant-checkout.zip`

## I'm a User - How Do I Install This?

1. Download the `.zip` file from [Releases](https://github.com/lmotwani/woocommerce-acp-instant-checkout/releases)
2. In WordPress: **Plugins > Add New > Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate**
5. Go to **WooCommerce > Settings > ACP/ChatGPT** to configure

## Configuration Steps

After installation:

1. **Apply for OpenAI ACP**
   - Visit https://openai.com/index/buy-it-in-chatgpt/
   - Complete the application process

2. **Get Your API Keys**
   - OpenAI ACP API Key: From OpenAI ACP dashboard (after approval)
   - Stripe API Keys: From https://dashboard.stripe.com/apikeys

3. **Configure Plugin**
   - Go to **WooCommerce > Settings > ACP/ChatGPT**
   - Enable ACP API
   - Enter OpenAI API Key
   - Enter Stripe API Keys (Publishable and Secret)
   - Save settings

4. **Submit to OpenAI**
   - Copy your Product Feed URL from settings
   - Submit to OpenAI for indexing

5. **Test**
   - Use OpenAI's developer tools
   - Try "Buy it in ChatGPT" functionality

## Requirements

- WordPress 5.0+
- WooCommerce 5.0+
- PHP 7.4+
- SSL certificate (HTTPS required for payments)

## Support

- Documentation: See README.md
- Issues: GitHub Issues
- Installation Help: See INSTALL.md
- Build Help: See BUILD-GUIDE.md
