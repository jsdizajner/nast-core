<?php
/**
 * Plugin Name: NAST Core
 * Plugin URI: https://nast.sk
 * Description: Core Business functionality
 * Version: dev.0.0.1
 * Author: SIPOS.DIGITAL
 * Author URI: https://sipos.digital
 * Text Domain: nast-core
 * Requires PHP: 8.0
 */

defined( 'ABSPATH' ) || exit;

// Include all features
require_once 'features/loader.php';

$core_plugins = [];

\CORE_LOADER\CORE_LOADER::initiate_features();
