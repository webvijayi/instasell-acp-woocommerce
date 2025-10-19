#!/bin/bash
# Build script for creating distributable plugin zip

# Exit on error
set -e

echo "Building WooCommerce ACP Instant Checkout plugin..."

# Install production dependencies
echo "Installing production dependencies..."
composer install --no-dev --optimize-autoloader

# Create build directory
BUILD_DIR="build"
PLUGIN_SLUG="woocommerce-acp-instant-checkout"

rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR/$PLUGIN_SLUG"

# Copy plugin files
echo "Copying plugin files..."
rsync -av --exclude="$BUILD_DIR" \
    --exclude=".git" \
    --exclude=".gitignore" \
    --exclude=".gitattributes" \
    --exclude="node_modules" \
    --exclude="composer.json" \
    --exclude="composer.lock" \
    --exclude="build.sh" \
    --exclude="build.bat" \
    --exclude="phpcs.xml" \
    --exclude="phpunit.xml" \
    --exclude="tests" \
    --exclude=".DS_Store" \
    . "$BUILD_DIR/$PLUGIN_SLUG/"

# Create zip
echo "Creating zip file..."
cd "$BUILD_DIR"
zip -r "../$PLUGIN_SLUG.zip" "$PLUGIN_SLUG"
cd ..

echo "Build complete! Plugin zip created: $PLUGIN_SLUG.zip"
echo "You can now upload this zip file to WordPress."
