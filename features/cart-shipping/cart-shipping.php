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
    ray( WC()->cart);
    $min_amount = 50; // YOUR FREE SHIPPING THRESHOLD
    $current = WC()->cart->subtotal;
    if ( $current < $min_amount ) {
        $amount = $min_amount - $current;
        $added_text = __(sprintf('Get&nbsp;<b>free shipping</b>&nbsp;if you order&nbsp;<span class="free-shipping-ammount">%s</span>&nbsp;more!', $amount), 'nast-core');
    } else {
        $added_text = __('Congratulations! Shipping is on us!', 'nast-core');
    }
    $return_to = wc_get_page_permalink( 'shop' );
    $notice = sprintf( '%s &nbsp;<a href="%s" class="button wc-forward">%s</a>', $added_text, esc_url( $return_to ), __('Continue Shopping', 'nast-core'));
    wc_print_notice( $notice, 'notice' );

    return $content;
}