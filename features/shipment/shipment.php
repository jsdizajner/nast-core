<?php
use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\Orders\CouponsController;
use Automattic\WooCommerce\Internal\Orders\TaxesController;
use Automattic\WooCommerce\Internal\Admin\Orders\MetaBoxes\CustomMetaBox;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\NumberUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;
use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;



/**
 * Hide shipping rates when free shipping is available, but keep "Local pickup"
 * Updated to support WooCommerce 2.6 Shipping Zones
 */

function hide_shipping_when_free_is_available( $rates, $package ) {
    $new_rates = array();
    foreach ( $rates as $rate_id => $rate ) {
        // Only modify rates if free_shipping is present.
        if ( 'free_shipping' === $rate->method_id ) {
            $new_rates[ $rate_id ] = $rate;
            break;
        }
    }

    if ( ! empty( $new_rates ) ) {
        //Save local pickup if it's present.
        foreach ( $rates as $rate_id => $rate ) {
            if ('local_pickup' === $rate->method_id ) {
                $new_rates[ $rate_id ] = $rate;
                break;
            }
        }
        return $new_rates;
    }

    return $rates;
}

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );

// Part 2: Reload checkout on payment gateway change
add_action('woocommerce_review_order_after_shipping', 'refresh_billing_postcode');

/**
 * Trigger javascript refresh page on Payment Method Change
 *
 * @return void
 */
function refresh_billing_postcode()
{

    echo '<!-- Refresher --><script type="text/javascript">
        jQuery(document).ready(function($) {
            $("form.checkout").on("change", "input[name^=\'billing_postcode\']", function() {
                // Trigger update_checkout to refresh the order review section
                $("body").trigger("update_checkout", { update_shipping_method: true });
            });
        });
    </script>';
}

add_filter('woocommerce_update_order_review_fragments', function ( $fragments ) {

    // Prepare shipping fragment
    ob_start();
    wc_cart_totals_shipping_html();
    $shipping_fragment = ob_get_clean();
    $fragments['.woocommerce-shipping-methods'] = $shipping_fragment;

    return $fragments;
});