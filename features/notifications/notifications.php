<?php

add_filter('woocommerce_email_headers', 'add_admin_to_email_notifications', 10, 3);

function add_admin_to_email_notifications($headers, $email_id, $order) {
    $admin_email = get_option('admin_email');
    $headers .= 'Bcc: ' . $admin_email . "\r\n";
    return $headers;
}