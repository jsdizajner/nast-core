<?php
defined( 'ABSPATH' ) || exit;



/**
 * Add Custom Data to Variation Template
 */

add_filter('woocommerce_available_variation', 'add_variation_data', 10, 3);
function add_variation_data($array, $instance, $variation)
{
    $array['get_stock_quantity'] = $variation->get_stock_quantity();
    $array['is_stock_managed'] = $variation->managing_stock();
    $array['is_on_backorder'] = $variation->is_on_backorder();
    $array['get_stock_status'] = $variation->get_stock_status();
    return $array;
}

add_filter( 'woocommerce_product_stock_status_options', 'rudr_product_statuses' );

function rudr_product_statuses( $product_statuses ){

    // let's add our custom product status in a format slug => name
    $product_statuses[ 'delayed-shipment' ] = __('Delayed shipment', 'nast-core');
    // you can also remove some of the default product stock statuses by the way

    // don't forget to return the changed array of statuses
    return $product_statuses;
}
function custom_wc_product_get_availability( $availability, $product ) {
    if ( $product->get_stock_status() == 'delayed-shipment' ) {
        $availability['availability'] = __(' <span class="woocommerce-variation-stock-delayed"><i class="fas fa-dolly"></i> Na objednávku do 14 dní.</span> <br> <span style="font-weight: normal;color:#696969;">Skladom u dodávateľa.</span>', 'nast-core');
        $availability['class'] = 'delayed-shipment';
    }
    return $availability;
}
add_filter( 'woocommerce_get_availability', 'custom_wc_product_get_availability', 10, 2 );

// Change the "In Stock" text
add_filter( 'woocommerce_get_availability_text', 'custom_instock_text', 10, 2 );
function custom_instock_text( $availability, $product ) {
    if ( $product->is_in_stock() ) {
        $availability = __('<span class="woocommerce-variation-stock-instock"><i class="fas fa-shipping-fast"></i> Skladom - odoslanie ihneď.</span> <br> <span style="font-weight: normal;color:#696969;">V prípade výnimiek v doprave Vás budeme kontaktovať e-mailom.</span>', 'nast-core'); // Replace "Available Now!" with your custom text
    }
    return $availability;
}

// Change the "On Backorder" text
add_filter( 'woocommerce_get_availability_text', 'custom_backorder_text', 10, 2 );
function custom_backorder_text( $availability, $product ) {
    if ( $product->is_on_backorder() ) {
        $availability = __('<span class="woocommerce-variation-stock-backorder"><i class="fas fa-dolly"></i> Na objednávku 3 - 7 dní.</span> <br> <span style="font-weight: normal;color:#696969;">V prípade výnimiek v doprave Vás budeme kontaktovať e-mailom.</span>', 'nast-core'); // Replace with your custom text
    }
    return $availability;
}


add_filter( 'woocommerce_admin_stock_html', function( $stock_html, $product ) {

    if( 'delayed-shipment' === $product->get_stock_status() ) {
        $stock_html = '<mark class="onbackorder">'. __('Delayed shipment', 'nast-core') .'</mark>';
    }

    return $stock_html;

}, 25, 2 );

