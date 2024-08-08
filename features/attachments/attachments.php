<?php
defined( 'ABSPATH' ) || exit;

/**
 * @snippet       File Attachment @ WooCommerce Emails
 * @email-ids     'new_order', 'customer_completed_order'
 */

add_filter( 'woocommerce_email_attachments', 'load_legal_attachments', 10, 4 );

function load_legal_attachments( $attachments, $email_id, $order, $email ) {
    $email_ids = array( 'new_order', 'customer_completed_order' );
    if ( in_array ( $email_id, $email_ids ) ) {
        $upload_dir = wp_upload_dir();
        $attachments[] = $upload_dir['basedir'] . "/2024/08/attachments/odstupenie_od_zmluvy.pdf";
        $attachments[] = $upload_dir['basedir'] . "/2024/08/attachments/obchodne_podmienky.pdf";
        $attachments[] = $upload_dir['basedir'] . "/2024/08/attachments/podmienky_ochrany_osobnych_udajov.pdf";
        $attachments[] = $upload_dir['basedir'] . "/2024/08/attachments/reklamacny_poriadok.pdf";
    }
    return $attachments;
}
