## NMI Payment Gateway – Sandbox Fixes for WooCommerce

A small, surgical fix for a well-known problem.
This snippet patches the WooCommerce NMI Gateway plugin so it behaves correctly in sandbox mode.

### What it fixes

- Incorrect sandbox endpoint
The plugin hardcodes production URLs. This forces requests to sandbox.nmi.com when testing.

- Sandbox email restrictions
NMI sandbox accounts can only send emails to their own registered address. All email fields are removed to prevent errors.

- Optional debugging
Lightweight logging to trace what the filters are doing.

### Configuration

Add the desired constants to wp-config.php:
```
define('NMI_USE_SANDBOX', true); // default: false
define('NMI_SANDBOX_DISABLE_EMAILS', true); // default: true
define('NMI_DEBUG_ENABLED', false); // default: false
```

### What it does

Forces the request URL to: https://sandbox.nmi.com/api/transact.php


### Disables:
```
customer_receipt

email

billing_email

shipping_email

Writes debug logs to:

wp-content/nmi-sandbox-debug.log
```


### Installation

Load the file as an MU-plugin, custom plugin, or include it in an existing plugin.

This is not a standalone plugin — it’s a targeted fix.

When to use it

✔ Testing payments with NMI
✔ Hitting sandbox email-related errors
✔ The NMI plugin refuses to fully support sandbox mode

Quiet code. Predictable behavior.
Exactly what you want in a payment gateway fix.
