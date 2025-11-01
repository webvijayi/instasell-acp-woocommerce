@echo off
REM Build script for creating distributable plugin zip (Windows)

echo Building Instant Checkout via ACP Agentic Commerce for WooCommerce plugin...

REM Check if vendor directory exists
if not exist "vendor" (
    echo ERROR: vendor directory not found!
    echo Please run 'composer install' first or ensure dependencies are installed.
    pause
    exit /b 1
)

REM Create build directory
set BUILD_DIR=build
set PLUGIN_SLUG=instant-checkout-via-acp-agentic-commerce-for-woocommerce

echo Cleaning old build...
if exist "%BUILD_DIR%" rmdir /s /q "%BUILD_DIR%"
if exist "%PLUGIN_SLUG%.zip" del /q "%PLUGIN_SLUG%.zip"

echo Creating build directory...
mkdir "%BUILD_DIR%\%PLUGIN_SLUG%"
mkdir "%BUILD_DIR%\%PLUGIN_SLUG%\includes"

REM Copy plugin files (excluding old files and dev files)
echo Copying plugin files...
xcopy /Y "includes\class-icvaac-*.php" "%BUILD_DIR%\%PLUGIN_SLUG%\includes\"
xcopy /E /I /Y "assets" "%BUILD_DIR%\%PLUGIN_SLUG%\assets\"
xcopy /E /I /Y "schemas" "%BUILD_DIR%\%PLUGIN_SLUG%\schemas\"
xcopy /E /I /Y "vendor" "%BUILD_DIR%\%PLUGIN_SLUG%\vendor\"
copy /Y "instant-checkout-via-acp-agentic-commerce-for-woocommerce.php" "%BUILD_DIR%\%PLUGIN_SLUG%\"
copy /Y "README.md" "%BUILD_DIR%\%PLUGIN_SLUG%\"
copy /Y "readme.txt" "%BUILD_DIR%\%PLUGIN_SLUG%\"
copy /Y "composer.json" "%BUILD_DIR%\%PLUGIN_SLUG%\"

REM Create zip using PowerShell script (creates proper cross-platform zip with forward slashes)
echo Creating zip file...
powershell -ExecutionPolicy Bypass -File "%~dp0create-zip.ps1"

echo.
echo ========================================
echo Build complete!
echo ========================================
echo.
echo Plugin zip created: %PLUGIN_SLUG%.zip
echo Size:
powershell -Command "(Get-Item '%PLUGIN_SLUG%.zip').Length / 1MB | ForEach-Object { '{0:N2} MB' -f $_ }"
echo.
echo You can now:
echo 1. Upload this zip to WordPress (Plugins ^> Add New ^> Upload)
echo 2. Create a GitHub release and attach this zip
echo 3. Submit to WordPress.org with the new slug reservation
echo.
echo IMPORTANT: Plugin slug is "instant-checkout-via-acp-agentic-commerce-for-woocommerce"
echo.
pause
