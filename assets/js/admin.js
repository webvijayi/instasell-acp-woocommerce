/**
 * Instant Checkout via ACP Agentic Commerce for WooCommerce - Admin Scripts
 */

jQuery(document).ready(function($) {
    'use strict';

    // Test API connection
    $('.icvaac-test-connection').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $result = $('#icvaac-connection-result');
        var type = $button.data('type');

        $button.prop('disabled', true).text('Testing...');
        $result.removeClass('notice-success notice-error').addClass('notice-info').show().text('Testing ' + type + ' API connection...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'icvaac_test_connection',
                nonce: icvaac_admin.nonce,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    $result.removeClass('notice-info notice-error').addClass('notice-success').text(type + ' API connection successful!');
                } else {
                    $result.removeClass('notice-info notice-success').addClass('notice-error').text(type + ' API connection failed: ' + response.data);
                }
            },
            error: function() {
                $result.removeClass('notice-info notice-success').addClass('notice-error').text(type + ' API connection failed: Network error');
            },
            complete: function() {
                $button.prop('disabled', false).text('Test Connection');
            }
        });
    });

    // Copy to clipboard
    $('.icvaac-copy-btn').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var target = $button.data('target');
        var $target = $('#' + target);
        var text = $target.text();

        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(text).select();
        document.execCommand('copy');
        $temp.remove();

        $button.text('Copied!');
        setTimeout(function() {
            $button.text('Copy');
        }, 2000);
    });

    // Generate product feed preview
    $('#icvaac-generate-feed').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $preview = $('#icvaac-feed-preview');

        $button.prop('disabled', true).text('Generating...');
        $preview.hide();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'icvaac_generate_feed_preview',
                nonce: icvaac_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $preview.show().find('pre').text(JSON.stringify(response.data, null, 2));
                } else {
                    alert('Failed to generate feed preview: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to generate feed preview: Network error');
            },
            complete: function() {
                $button.prop('disabled', false).text('Generate Preview');
            }
        });
    });

    // Toggle API key visibility
    $('.icvaac-toggle-visibility').on('click', function(e) {
        e.preventDefault();

        var $input = $(this).siblings('input[type="password"], input[type="text"]');
        var $icon = $(this).find('.dashicons');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
        }
    });
});
