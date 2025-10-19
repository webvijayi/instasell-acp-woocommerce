@echo off
REM Build script for creating distributable plugin zip (Windows)

echo Building WooCommerce ACP Instant Checkout plugin...

REM Install production dependencies
echo Installing production dependencies...
call composer install --no-dev --optimize-autoloader

REM Create build directory
set BUILD_DIR=build
set PLUGIN_SLUG=woocommerce-acp-instant-checkout

if exist "%BUILD_DIR%" rmdir /s /q "%BUILD_DIR%"
mkdir "%BUILD_DIR%\%PLUGIN_SLUG%"

REM Copy plugin files
echo Copying plugin files...
xcopy /E /I /Y /EXCLUDE:build-exclude.txt . "%BUILD_DIR%\%PLUGIN_SLUG%"

REM Create zip (requires PowerShell)
echo Creating zip file...
powershell -Command "Compress-Archive -Path '%BUILD_DIR%\%PLUGIN_SLUG%' -DestinationPath '%PLUGIN_SLUG%.zip' -Force"

echo Build complete! Plugin zip created: %PLUGIN_SLUG%.zip
echo You can now upload this zip file to WordPress.
pause
