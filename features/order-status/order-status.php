<?php
defined( 'ABSPATH' ) || exit;
global $core_plugins;

$core_plugins[] = [
    'version' => '1.0.0',
    'package' => 'ORDER_STATUS'
];


/**
 * Custom Order Status: Contact Customer
 */
add_filter( 'woocommerce_register_shop_order_post_statuses', 'create_status_contact_customer' );

function create_status_contact_customer( $order_statuses )
{
    // Status must start with "wc-"!
    $order_statuses['wc-contact-customer'] = array(
        'label' => __('Contact Customer', 'nast-core'),
        'public' => false,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop( 'Contact Customer <span class="count">(%s)</span>', 'Contact Customer <span class="count">(%s)</span>', 'woocommerce' ),
    );
    return $order_statuses;
}

add_filter( 'wc_order_statuses', 'show_contact_customer_in_dropdown' );

function show_contact_customer_in_dropdown( $order_statuses )
{
    $order_statuses['wc-contact-customer'] = 'Contact Customer';
    return $order_statuses;
}

add_filter( 'bulk_actions-edit-shop_order', 'contact_customer_register_bulk_action' ); // edit-shop_order is the screen ID of the orders page

function contact_customer_register_bulk_action( $bulk_actions )
{
    $bulk_actions[ 'bulk-contact-customer' ] = __('Change status to Contact Customer', 'nast-core'); // <option value="mark_awaiting_shipping">Change status to awaiting shipping</option>
    return $bulk_actions;
}

add_action( 'handle_bulk_actions-edit-shop_order', 'bulk_process_contact_customer_status', 20, 3 );

function bulk_process_contact_customer_status( $redirect, $doaction, $object_ids ) {

    if ( 'bulk-contact-customer' === $doaction ) {

        // change status of every selected order
        foreach ( $object_ids as $order_id ) {
            $order = wc_get_order( $order_id );
            $order->update_status( 'wc-contact-customer' );
        }

        // do not forget to add query args to URL because we will show notices later
        $redirect = add_query_arg(
            array(
                'bulk_action' => 'bulk_contact_customer',
                'changed' => count( $object_ids ),
            ),
            $redirect
        );

    }

    return $redirect;

}

add_action( 'admin_notices', 'contact_customer_bulk_action_status_notices' );

function contact_customer_bulk_action_status_notices()
{

    if (
        isset( $_REQUEST[ 'bulk_action' ] )
        && 'bulk_contact_customer' == $_REQUEST[ 'bulk_action' ]
        && isset( $_REQUEST[ 'changed' ] )
        && $_REQUEST[ 'changed' ]
    ) {

        // displaying the message
        printf(
            '<div id="message" class="updated notice is-dismissible"><p>' . _n( '%d order status changed.', '%d order statuses changed.', $_REQUEST[ 'changed' ] ) . '</p></div>',
            $_REQUEST[ 'changed' ]
        );

    }

}

add_action('admin_head', 'wc_contact_status_style');

function wc_contact_status_style()
{
    echo
    '
	<style>
		.status-contact-customer {
		display: inline-flex;
		line-height: 2.5em;
		color: #202aa2;
    	background: #4bb9fb;
		border-radius: 4px;
		border-bottom: 1px solid rgba(0,0,0,.05);
		margin: -0.25em 0;
		cursor: inherit!important;
		white-space: nowrap;
		max-width: 100%;
	}
	</style>	
	';
}






// Make order with status 'contant-customer' payable
add_filter('woocommerce_valid_order_statuses_for_payment', 'make_order_status_contact_customer_payable', 10, 2);
function make_order_status_contact_customer_payable($array, $instance)
{
    $array[] = 'contact-customer';
    return $array;
}


?>