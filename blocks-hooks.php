<?php
defined( 'ABSPATH' ) || exit;

/**
 * @snippet       Create Hooks For WooCommerce Cart Block
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 9
 * @community     https://businessbloomer.com/club/
 */

add_filter( 'render_block', 'core_woocommerce_cart_block_do_actions', 9999, 2 );

function core_woocommerce_cart_block_do_actions( $block_content, $block ) {
    $blocks = array(
        'woocommerce/cart',
        'woocommerce/filled-cart-block',
        'woocommerce/cart-items-block',
        'woocommerce/cart-line-items-block',
        'woocommerce/cart-cross-sells-block',
        'woocommerce/cart-cross-sells-products-block',
        'woocommerce/cart-totals-block',
        'woocommerce/cart-order-summary-block',
        'woocommerce/cart-order-summary-heading-block',
        'woocommerce/cart-order-summary-coupon-form-block',
        'woocommerce/cart-order-summary-subtotal-block',
        'woocommerce/cart-order-summary-fee-block',
        'woocommerce/cart-order-summary-discount-block',
        'woocommerce/cart-order-summary-shipping-block',
        'woocommerce/cart-order-summary-taxes-block',
        'woocommerce/cart-express-payment-block',
        'woocommerce/proceed-to-checkout-block',
        'woocommerce/cart-accepted-payment-methods-block',
    );
    if ( in_array( $block['blockName'], $blocks ) ) {
        ob_start();
        $block_name = str_replace('/', '_', $block['blockName']);
        do_action( 'nast_before_' . $block_name, $block_content );
        echo $block_content;
        do_action( 'nast_after_' . $block_name, $block_content );
        $block_content = ob_get_contents();
        ob_end_clean();
    }
    return $block_content;
}

