<?php
/**
 * Plugin Name: Dev SMTP (DO NOT USE IN PRODUCTION)
 * Description: A plugin to send all emails through smtp server defined in wp-config.php for testing purposes. Provided by Timoshka Lab Inc. You can check your emails on <a href="http://127.0.0.1:8025/">Mailhog</a> if you are using Docker.
 * License: GPLv2 or later
 * Version: 1.0.2
 */

if (! defined( 'ABSPATH' )) die;

add_action('phpmailer_init', function ($phpmailer) {
    if (apply_filters('timoshka_lab_wp_dev_smtp_enabled', true)) {
        foreach (['SMTP_HOST', 'SMTP_PORT', 'SMTP_FROM', 'SMTP_NAME'] as $required) {
            if (!defined($required) || empty($required)) {
                wp_die(sprintf('Dev SMTP: please define %s value in wp-config.php', $required));
            }
        }

        $encryption = defined('SMTP_ENCRYPTION') && !empty(SMTP_ENCRYPTION) ? SMTP_ENCRYPTION : false;

        if ($encryption && !in_array($encryption, ['ssl', 'tls'])) {
            wp_die('Dev SMTP: SMTP_ENCRYPTION value should be either "ssl" or "tls"');
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = gethostbyname(SMTP_HOST);
        $phpmailer->Port       = SMTP_PORT;
        $phpmailer->From       = SMTP_FROM;
        $phpmailer->FromName   = SMTP_NAME;
        $phpmailer->SMTPSecure = $encryption;
        $phpmailer->SMTPAuth   = false;
        $phpmailer->SMTPAutoTLS = false;
        $phpmailer->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        if (defined('SMTP_USER') && !empty(SMTP_USER) && defined('SMTP_PASS') && !empty(SMTP_PASS)) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = SMTP_USER;
            $phpmailer->Password = SMTP_PASS;
        }
    }

    return $phpmailer;
});

