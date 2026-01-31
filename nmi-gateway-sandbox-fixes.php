<?php
/**
 * NMI Payment Gateway Fixes
 *
 * Fixes for the WooCommerce NMI Gateway plugin that don't support sandbox mode properly.
 *
 * Configuration (wp-config.php):
 * - NMI_USE_SANDBOX: Use sandbox.nmi.com endpoint (default: false)
 * - NMI_SANDBOX_DISABLE_EMAILS: Remove email fields in sandbox mode (default: true)
 * - NMI_DEBUG_ENABLED: Log NMI filter activity (default: false)
 *
 * @package JMA
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Fix sandbox URL - plugin doesn't support sandbox.nmi.com
 *
 * The NMI plugin only has production URLs hardcoded. When using sandbox/test
 * credentials, requests must go to sandbox.nmi.com instead.
 */
add_filter('wc_nmi_request_url', function($url) {
    $use_sandbox = defined('NMI_USE_SANDBOX') && NMI_USE_SANDBOX;
    $debug = defined('NMI_DEBUG_ENABLED') && NMI_DEBUG_ENABLED;

    if ($debug) {
        $log_file = WP_CONTENT_DIR . '/nmi-sandbox-debug.log';
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "URL Filter: NMI_USE_SANDBOX=" . ($use_sandbox ? 'true' : 'false') . ", original={$url}\n", FILE_APPEND);
    }

    if ($use_sandbox) {
        return 'https://sandbox.nmi.com/api/transact.php';
    }
    return $url;
});

/**
 * Fix sandbox email restrictions
 *
 * NMI sandbox accounts can only send emails to their own registered email address.
 * This filter removes all email fields from the request to avoid the error:
 * "Sandbox accounts can only send emails to their own email address"
 */
add_filter('wc_nmi_request_args', function($args, $order) {
    $use_sandbox = defined('NMI_USE_SANDBOX') && NMI_USE_SANDBOX;
    $disable_emails = defined('NMI_SANDBOX_DISABLE_EMAILS') ? NMI_SANDBOX_DISABLE_EMAILS : true;
    $debug = defined('NMI_DEBUG_ENABLED') && NMI_DEBUG_ENABLED;

    if ($debug) {
        $log_file = WP_CONTENT_DIR . '/nmi-sandbox-debug.log';
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Args Filter: sandbox={$use_sandbox}, disable_emails={$disable_emails}\n", FILE_APPEND);
    }

    if ($use_sandbox && $disable_emails) {
        // Disable customer receipt
        $args['customer_receipt'] = 'false';
        // Remove ALL email fields
        unset($args['email']);
        unset($args['shipping_email']);
        unset($args['billing_email']);

        if ($debug) {
            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Args Filter: Email fields removed, customer_receipt=false\n", FILE_APPEND);
        }
    }
    return $args;
}, 999, 2);
