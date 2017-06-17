<?php
/*
Plugin Name: Woocommerce Limit Order
Plugin URI: themecitizen.com
Description: The plugin allows admin manage their site is better by create limit rules use to limit customer order and limit product order.
Version: 2.6
Author: WP Friends
Author URI: themecitizen.com
Requires at least: 3.8
Tested up to: 4.7.3

License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin PATHs, for fast require files
define( 'WLO_PATH', plugin_dir_path( __FILE__ ) );
define( 'WLO_INC_PATH', trailingslashit( WLO_PATH . 'inc' ) );
define( 'WLO_TEMPLATES_PATH', trailingslashit( WLO_PATH . 'templates' ) );
define( 'WLO_TEMPLATES_BACKEND_PATH', trailingslashit( WLO_TEMPLATES_PATH . 'backend' ) );
define( 'WLO_TEMPlATES_FRONTEND_PATH', trailingslashit( WLO_TEMPLATES_PATH . 'frontend' ) );

// Define plugin URLs, for fast enqueuing scripts and styles
define( 'WLO_URL', plugin_dir_url( __FILE__ ) );
define( 'WLO_CSS_URL', trailingslashit( WLO_URL . 'css' ) );
define( 'WLO_JS_URL', trailingslashit( WLO_URL . 'js' ) );

define( 'WLO_OPION', '_wlo_options' );
define( 'WLO_USER_OPION', '_wlo_user_options' );
define( 'WLO_PRODUCT_OPION', '_wlo_product_options' );
define( 'WLO_PRODUCT_CATEGORY_OPTION', '_wlo_product_category_options' );
define( 'WLO_VERSION', '2.3.0' );
// Add new table name in DB
define( 'TABLE_NAME', 'wlo_order_log' );

// Add database
require WLO_INC_PATH . 'db/wlo-initdb.php';

register_activation_hook( __FILE__, 'wlo_init_database' );

// Common functions
require WLO_INC_PATH . 'wlo-functions.php';
new WLO_FUNCTIONS();
// Enqueue files style and script
require WLO_INC_PATH . 'wlo-setup.php';
new WLO_SETUP();
// Class manipulates with database
require WLO_INC_PATH . 'db/wlo-db-helper.php';
// settings page
require WLO_INC_PATH . 'settings/wlo-general-settings.php';
require WLO_INC_PATH . 'settings/wlo-customer-settings.php';
require WLO_INC_PATH . 'settings/wlo-product-settings.php';
require WLO_INC_PATH . 'settings/wlo-product-category-settings.php';
require WLO_INC_PATH . 'settings/wlo-order-settings.php';
// require file check order time and display notice
require WLO_INC_PATH . 'wlo-limit-customer.php';
require WLO_INC_PATH . 'wlo-limit-product.php';
require WLO_INC_PATH . 'wlo-limit-product-category.php';
// require file check order time and display notice
require WLO_INC_PATH . 'wlo-limit-order.php';
new WLO_LIMIT_ORDER();

if( is_admin() )
{
    // Limit order sub menu
    require WLO_TEMPLATES_BACKEND_PATH . 'wlo-settings-page.php';
    new WLO_SETTINGS_PAGE();
}