<?php
/**
 * Plugin Name: Dev SMTP (DO NOT USE IN PRODUCTION)
 * Description: A plugin to send all emails through smtp server defined in wp-config.php for testing purposes. Provided by Timoshka Lab Inc. You can check your emails on <a href="http://127.0.0.1:8025/">Mailhog</a> if you are using Docker.
 * License: GPLv2 or later
 */

if (! defined( 'ABSPATH' )) die;

add_action('phpmailer_init', function ($phpmailer) {
    if (apply_filters('timoshka_lab_wp_dev_smtp_enabled', true)) {
        foreach (['SMTP_HOST', 'SMTP_PORT', 'SMTP_FROM', 'SMTP_NAME'] as $required) {
            if (!defined($required) || empty($required)) {
                wp_die(sprintf('Dev SMTP: please define %s value in wp-config.php', $required));
            }
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = SMTP_HOST;
        $phpmailer->Port       = SMTP_PORT;
        $phpmailer->From       = SMTP_FROM;
        $phpmailer->FromName   = SMTP_NAME;
        $phpmailer->SMTPAuth   = false;
        $phpmailer->SMTPSecure = false;
        $phpmailer->SMTPAutoTLS = false;
        $phpmailer->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];
    }

    return $phpmailer;
});

