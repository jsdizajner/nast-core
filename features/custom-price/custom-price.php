<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function add_custom_customer_role() {
    // Get the "Customer" role
    $customer_role = get_role( 'customer' );

    // Add the new role with the same capabilities as the "Customer" role
    add_role(
        'nast_vip_customer', // Internal name of the custom role
        __( 'VIP Customer', 'nast-core' ), // Display name for the custom role
        $customer_role->capabilities // Copy capabilities from the "Customer" role
    );
}
add_action( 'init', 'add_custom_customer_role' );

/**
 * Add Custom Price Field to simple products
 *
 * @return void
 */
function add_custom_price_field() {
    // Start of a new row

    woocommerce_wp_text_input(
        array(
            'id'          => '_custom_price',
            'label'       => __( 'Custom Price', 'nast-core' ),
            'type'      => 'text',
            'data_type' => 'price',
            'placeholder' => __( 'Enter price for VIP Customers', 'nast-core' ),
        )
    );

}
add_action( 'woocommerce_product_options_pricing', 'add_custom_price_field' );

/**
 * Save Custom Price Field
 *
 * @param $post_id
 * @return void
 */
function save_custom_price_field( $post_id ) {
    $custom_price = isset( $_POST['_custom_price'] ) ? sanitize_text_field( $_POST['_custom_price'] ) : '';
    update_post_meta( $post_id, '_custom_price', $custom_price );
}
add_action( 'woocommerce_process_product_meta', 'save_custom_price_field' );


/**
 * Add Custom Price Field to Variations
 *
 * @param $loop
 * @param $variation_data
 * @param $variation
 * @return void
 */
function add_custom_price_field_to_variations( $loop, $variation_data, $variation ) {

    // Start of a new row
    echo '<div class="form-row form-row-full">';

    woocommerce_wp_text_input(
        array(
            'id'        => '_custom_price['.$variation->ID.']',
            'label'     => __( 'Custom Price', 'nast-core' ),
            'value'     => get_post_meta( $variation->ID, '_custom_price', true ),
            'type'      => 'text',
            'data_type' => 'price',
            'placeholder' => __( 'Enter price for VIP Customers', 'nast-core' ),
            'wrapper_class' => 'form-row form-row-full', // Ensures the field is in a new row
        )
    );

    // End of the row
    echo '</div>';
}
add_action( 'woocommerce_variation_options_pricing', 'add_custom_price_field_to_variations', 10, 3 );

/**
 * Save Custom Price Field for Variations
 *
 * @param $variation_id
 * @return void
 */
function save_custom_price_field_variations( $variation_id ) {
    $custom_price = isset( $_POST['_custom_price'][$variation_id] ) ? sanitize_text_field( $_POST['_custom_price'][$variation_id] ) : '';
    update_post_meta( $variation_id, '_custom_price', $custom_price );
}
add_action( 'woocommerce_save_product_variation', 'save_custom_price_field_variations', 10, 2 );



















// Apply custom sale price for "nast_vip_customer"
add_filter('woocommerce_product_get_price', 'apply_custom_sale_price_for_vip_customers', 20, 2);
add_filter('woocommerce_product_get_sale_price', 'apply_custom_sale_price_for_vip_customers', 20, 2);

function apply_custom_sale_price_for_vip_customers($price, $product) {
    // Check if the user is logged in and has the role "nast_vip_customer"

    if (is_user_logged_in() && current_user_can('nast_vip_customer')) {
        // Retrieve the custom price from the meta field
        $price = get_post_meta($product->get_id(), '_custom_price', true);

        // If the custom price exists and is numeric, use it as the sale price
        if (!empty($price)) {
            return $price;
        }
    }

    return $price;

}

add_filter('woocommerce_variation_prices', 'custom_variation_prices_with_custom_price', 10, 2);
function custom_variation_prices_with_custom_price($prices_array, $product) {

    // Loop through each variation
    if (is_user_logged_in() && current_user_can('nast_vip_customer')) {


        foreach ($prices_array['price'] as $variation_id => $price) {
            // Get the _custom_price metadata for the variation
            $custom_price = get_post_meta($variation_id, '_custom_price', true);

            // Example: If custom price exists, override the regular and sale prices
            if (!empty($custom_price)) {
                $prices_array['sale_price'][$variation_id] = $custom_price;
                $prices_array['price'][$variation_id] = $custom_price;
            }
        }

    }

    return $prices_array;
}


add_filter( 'woocommerce_sale_flash', 'nova_percentage_sale', 11, 3 );
function nova_percentage_sale( $text, $post, $product ) {
    $discount = 0;
    if ( $product->get_type() == 'variable' ) {
        $available_variations = $product->get_available_variations();
        $maximumper = 0;
        for ($i = 0; $i < count($available_variations); ++$i) {
            $variation_id=$available_variations[$i]['variation_id'];
            $variable_product1= new WC_Product_Variation( $variation_id );
            $regular_price = $variable_product1->get_regular_price();
            $sales_price = $variable_product1->get_sale_price();

            if (is_user_logged_in() && current_user_can('nast_vip_customer')) {
                $sales_price = get_post_meta($variation_id, '_custom_price', true);
            }

            if( $sales_price ) {
                $percentage= round( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) ;
                if ($percentage > $maximumper) {
                    $maximumper = $percentage;
                }
            }
        }
        $text = '<span class="onsale">-' . $maximumper  . '%</span>';
    } elseif ( $product->get_type() == 'simple' ) {
        $percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
        $text = '<span class="onsale">-' . $percentage . '%</span>';
    }

    return $text;
}