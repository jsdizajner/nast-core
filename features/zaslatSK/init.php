<?php
defined( 'ABSPATH' ) || exit;



/**
 * Create order status
 * Orders with this status will be used in Zaslat.sk Woocommerce Integration
 * 
 * @docs https://www.cloudways.com/blog/create-woocommerce-custom-order-status/
 */

// Register new order status for orders to be compatible with Zaslat.sk Woocommerce Integration
add_action( 'init', function(){
    register_post_status( 'wc-zaslat', array(
        'label'                     => 'Zaslat.sk',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Zaslat.sk <span class="count">(%s)</span>', 'Zaslat.sk <span class="count">(%s)</span>' )
    ) );
} );


add_filter( 'wc_order_statuses', function ( $order_statuses ) {
    $order_statuses['wc-zaslat'] = 'Zaslat.sk';
    return $order_statuses;
} );


/**
 * Add bulk actions in admin-orders
 * @docs https://rudrastyh.com/woocommerce/bulk-change-custom-order-status.html
 */
add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'misha_register_bulk_action' ); // edit-shop_order is the screen ID of the orders page
function misha_register_bulk_action( $bulk_actions ) {

	$bulk_actions[ 'mark_zaslat' ] = 'Change status to Zaslat.sk';
	return $bulk_actions;

}



add_action( 'handle_bulk_actions-woocommerce_page_wc-orders', 'misha_bulk_process_custom_status', 20, 3 );
function misha_bulk_process_custom_status( $redirect, $doaction, $object_ids ) {

	if( 'mark_zaslat' === $doaction ) {

		// change status of every selected order
		foreach ( $object_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			$order->update_status( 'wc-zaslat' );
		}

		// do not forget to add query args to URL because we will show notices later
		$redirect = add_query_arg(
			array(
				'bulk_action' => 'marked_zaslat',
				'changed' => count( $object_ids ),
			),
			$redirect
		);

	}

	return $redirect;

}
