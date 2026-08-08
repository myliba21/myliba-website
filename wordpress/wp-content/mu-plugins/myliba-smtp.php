<?php
/**
 * Plugin Name: Myliba SMTP Config
 * Description: Configures WP Mail SMTP via constants. Local uses Mailpit (port 1025); production uses Gmail/Google OAuth.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Prevent redefinition if wp-config.php already defined them.
if (!defined('WPMS_ON')) {
    define('WPMS_ON', true);
}

if (!defined('WPMS_MAIL_FROM')) {
    define('WPMS_MAIL_FROM', getenv('WPMS_MAIL_FROM') ?: 'hello@myliba.com');
}

if (!defined('WPMS_MAIL_FROM_NAME')) {
    define('WPMS_MAIL_FROM_NAME', getenv('WPMS_MAIL_FROM_NAME') ?: 'Myliba');
}

if (!defined('WPMS_MAIL_FROM_FORCE')) {
    define('WPMS_MAIL_FROM_FORCE', true);
}

// Mailer: 'smtp' for local (Mailpit), 'gmail' for production (Google OAuth)
if (!defined('WPMS_MAILER')) {
    define('WPMS_MAILER', getenv('WPMS_MAILER') ?: 'smtp');
}

// SMTP settings (used when WPMS_MAILER = 'smtp', i.e. Mailpit in local)
if (!defined('WPMS_SMTP_HOST')) {
    define('WPMS_SMTP_HOST', 'mailpit');
}
if (!defined('WPMS_SMTP_PORT')) {
    define('WPMS_SMTP_PORT', 1025);
}
if (!defined('WPMS_SSL')) {
    define('WPMS_SSL', '');
}
if (!defined('WPMS_SMTP_AUTH')) {
    define('WPMS_SMTP_AUTH', false);
}
if (!defined('WPMS_SMTP_AUTOTLS')) {
    define('WPMS_SMTP_AUTOTLS', false);
}

// Gmail / Google OAuth settings (used when WPMS_MAILER = 'gmail')
if (!defined('WPMS_GMAIL_CLIENT_ID')) {
    define('WPMS_GMAIL_CLIENT_ID', getenv('WPMS_GMAIL_CLIENT_ID') ?: '');
}
if (!defined('WPMS_GMAIL_CLIENT_SECRET')) {
    define('WPMS_GMAIL_CLIENT_SECRET', getenv('WPMS_GMAIL_CLIENT_SECRET') ?: '');
}
