<?php

defined( 'ABSPATH' ) || exit;

/**
 * @snippet       $$$ remaining to Free Shipping @ WooCommerce Cart
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */

add_action( 'nast_before_woocommerce_cart', 'bbloomer_free_shipping_cart_notice', 10, 1);

function bbloomer_free_shipping_cart_notice($content) {
    $min_amount = 100; // YOUR FREE SHIPPING THRESHOLD
    $current = WC()->cart->subtotal;
    if ( $current < $min_amount ) {
        $amount = $min_amount - $current;
        $amount = number_format( $amount, 2, '.', '');
        $added_text = sprintf(__('Získajte&nbsp;<b>DOPRAVU ZDARMA</b>&nbsp;ak nakúpite tovar za&nbsp;<span class="free-shipping-ammount" style="font-weight: bold;">%s</span>! &nbsp;', 'nast-core'), $amount);
    } else {
        $added_text = __('Congratulations! Shipping is on us!', 'nast-core');
    }
    $return_to = wc_get_page_permalink( 'shop' );
    $notice = sprintf( '%s &nbsp;<a href="%s" class="button wc-forward">%s</a>', $added_text, esc_url( $return_to ), __('Continue Shopping', 'nast-core'));
    wc_print_notice( $notice, 'notice' );

    return $content;
}