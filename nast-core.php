<?php
/**
 * Plugin Name: NAST Core
 * Plugin URI: https://nast.sk
 * Description: Core Business functionality
 * Version: 1.6.5
 * Author: SIPOS.DIGITAL
 * Author URI: https://sipos.digital
 * Text Domain: nast-core
 * Requires PHP: 8.0
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;


define('NAST_CORE_DIR', plugin_dir_path(__FILE__));
define('NAST_CORE_URL', plugin_dir_url(__FILE__));

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/jsdizajner/nast-core',
    __FILE__,
    'nast-core'
);

add_action( 'init', function() {
    update_option( 'wc_feature_woocommerce_brands_enabled', 'yes' );
    update_option( 'woocommerce_remote_variant_assignment', 2 );
} );

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

require_once NAST_CORE_DIR .'/vendor/autoload.php';
\Carbon_Fields\Carbon_Fields::boot();

require_once NAST_CORE_DIR .'blocks-hooks.php';

require_once NAST_CORE_DIR .'/features/order-status/order-status.php';
require_once NAST_CORE_DIR .'/features/csv-importer.php';
require_once NAST_CORE_DIR .'/features/zaslatSK/init.php';
require_once NAST_CORE_DIR .'/features/fees/fees.php';
require_once NAST_CORE_DIR .'/features/slider/slider.php';
require_once NAST_CORE_DIR .'/features/shipment/shipment.php';
require_once NAST_CORE_DIR .'/features/attachments/attachments.php';
require_once NAST_CORE_DIR .'/features/stock/stock.php';
require_once NAST_CORE_DIR .'/features/custom-price/custom-price.php';
require_once NAST_CORE_DIR .'/features/cart-shipping/cart-shipping.php';

