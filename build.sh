#!/bin/bash
# Build script for creating distributable plugin zip

# Exit on error
set -e

echo "Building Instant Checkout via ACP Agentic Commerce for WooCommerce plugin..."

# Install production dependencies
echo "Installing production dependencies..."
composer install --no-dev --optimize-autoloader

# Create build directory
BUILD_DIR="build"
PLUGIN_SLUG="instant-checkout-via-acp-agentic-commerce-for-woocommerce"

rm -rf "$BUILD_DIR"
rm -f "$PLUGIN_SLUG.zip"
mkdir -p "$BUILD_DIR/$PLUGIN_SLUG"

# Copy plugin files - exclude old files
echo "Copying plugin files..."
rsync -av --exclude="$BUILD_DIR" \
    --exclude=".git" \
    --exclude=".github" \
    --exclude=".gitignore" \
    --exclude=".gitattributes" \
    --exclude=".kiro" \
    --exclude="node_modules" \
    --exclude="build.sh" \
    --exclude="build.bat" \
    --exclude="build-exclude.txt" \
    --exclude="phpcs.xml" \
    --exclude="phpunit.xml" \
    --exclude="tests" \
    --exclude=".DS_Store" \
    --exclude=".distignore" \
    --exclude="composer.phar" \
    --exclude="composer-setup.php" \
    --exclude="cacert.pem" \
    --exclude="*.md" \
    --exclude="acp-instant-checkout-for-woocommerce.php" \
    --exclude="webvijayi-ai-checkout-acp-woocommerce.php" \
    --exclude="includes/class-wcacp-*.php" \
    --exclude="includes/class-wvacp-*.php" \
    --exclude="create-zip.ps1" \
    --exclude="create-zip.php" \
    . "$BUILD_DIR/$PLUGIN_SLUG/"

# Add back README.md
cp README.md "$BUILD_DIR/$PLUGIN_SLUG/" 2>/dev/null || true

# Remove bin directories from vendor (not needed in production)
echo "Removing vendor bin directories..."
rm -rf "$BUILD_DIR/$PLUGIN_SLUG/vendor/justinrainbow/json-schema/bin" 2>/dev/null || true
rm -rf "$BUILD_DIR/$PLUGIN_SLUG/vendor/stripe/stripe-php/bin" 2>/dev/null || true
find "$BUILD_DIR/$PLUGIN_SLUG/vendor" -type d -name "bin" -exec rm -rf {} + 2>/dev/null || true

# Create zip with proper forward slashes for cross-platform compatibility
echo "Creating zip file..."
cd "$BUILD_DIR"
zip -r -q "../$PLUGIN_SLUG.zip" "$PLUGIN_SLUG"
cd ..

# Show zip contents
echo ""
echo "Build complete! Plugin zip created: $PLUGIN_SLUG.zip"
echo ""
echo "Zip contents:"
unzip -l "$PLUGIN_SLUG.zip" | head -30
echo ""
echo "You can now upload this zip file to WordPress."
