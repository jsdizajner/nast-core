<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

defined( 'ABSPATH' ) || exit;


// Create admin page
add_action( 'carbon_fields_register_fields', 'create_fees_fields' );
function create_fees_fields()
{
    Container::make( 'theme_options', __( 'Poplatky', 'nast-core' ) )
        ->set_page_parent( 'woocommerce' )
        ->add_fields( array(
            Field::make( 'separator', 'cod_separator', __( 'Poplatok za platbu na dobierku', 'nast-core' ) ),
            Field::make( 'text', 'cod_fee_label', __( 'Položka na faktúre', 'nast-core' ) )
                ->set_attribute( 'type', 'text' ),
            Field::make( 'text', 'cod_fee', __( 'Poplatok v EUR', 'nast-core' ) )
                ->set_attribute( 'type', 'number' )
                ->set_help_text( 'Finálny poplatok: <span class="cod_fee_output" style="font-weight:bold"></span> &euro; (s DPH)' ),

        ) );
}

// Add fields and conditional logic for the fields
add_action('admin_footer', 'render_fees_js', 1100000);
function render_fees_js()
{

    ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", () => {
            console.log('DOM fully loaded and parsed');
            const codFeeInput = document.querySelector('input[name="carbon_fields_compact_input[_cod_fee]"]');
            const codFeeOutput = document.querySelector('.cod_fee_output');

            if (!codFeeInput) {
                console.error('Input element not found');
                return;
            }

            if (!codFeeOutput) {
                console.error('Output element not found');
                return;
            }

            // Add event listener to input element
            codFeeInput.addEventListener('input', function() {
                const inputValue = parseFloat(codFeeInput.value);
                // Check if inputValue is a valid number
                if (!isNaN(inputValue)) {
                    // Calculate 20% of the input value and add it to the original value
                    const result = inputValue * 1.2;
                    // Update the content of the span with the calculated result
                    codFeeOutput.textContent = result.toFixed(2); // Displaying with 2 decimal places
                } else {
                    // If input is not a valid number, display a message
                    codFeeOutput.textContent = 'chýba hodnota poplatku';
                }
            });

            // Function to update output element with initial value
            function updateOutput() {
                const initialValue = parseFloat(codFeeInput.value);
                if (!isNaN(initialValue)) {
                    const result = initialValue * 1.2;
                    codFeeOutput.textContent = result.toFixed(2); // Displaying with 2 decimal places
                }
            }

            // Call the function to update output element after page has loaded
            window.addEventListener('load', updateOutput);

            // Also call the function directly in case the window load event is not sufficient
            updateOutput();
        });


    </script>
    <?php
}

/**
 * @snippet WooCommerce Add fee to checkout for a gateway ID
 * @date 2022/09
 */

// Part 1: Assign fee
add_action('woocommerce_cart_calculate_fees', 'add_checkout_fee_for_gateway');

/**
 * Adds fee based on selected payment methods
 * Other Gateway IDs: WC()->payment_gateways->get_available_payment_gateways()
 *
 * @return void
 */
function add_checkout_fee_for_gateway()
{
    // if shipping is local do not add fee
    $shipping = wc_get_chosen_shipping_method_ids();
    if ($shipping[0] === 'local_pickup') {
        return;
    }

    $chosen_gateway = WC()->session->get('chosen_payment_method');
    $fee = carbon_get_theme_option('cod_fee');
    $label = carbon_get_theme_option('cod_fee_label');
    if ($chosen_gateway == 'cod') {
        WC()->cart->add_fee(__($label, 'nast-core'), $fee, true, '');
    }
}

// Part 2: Reload checkout on payment gateway change
add_action('woocommerce_review_order_before_payment', 'refresh_checkout_on_payment_methods_change');

/**
 * Trigger javascript refresh page on Payment Method Change
 *
 * @return void
 */
function refresh_checkout_on_payment_methods_change()
{
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            $('form.checkout').on( 'change', 'input[name^=\'payment_method\']', function()
            {
                $('body').trigger('update_checkout');
            });
        });
    </script>
    <?php
}


?>