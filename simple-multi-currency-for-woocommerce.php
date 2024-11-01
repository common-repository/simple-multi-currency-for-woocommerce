<?php
/*
 * Plugin Name:  Simple multi-currency for Woocommerce by Invelity
 * Version: 1.0.11
 * Description: This plugin allow in Woocommerce set custom fees for payment for allowed shipping countries, set currency symbols for allowed shipping countries, set product price and sale price for each shippment country, set if coupon is allowed for shippment or billing country, display currency switcher in shop, product and cart, filter orders by product in admin orders table.
 * Author: INVELITY
 * Author URI: https://invelity.com
 * Plugin URI: https://gitlab.com/Novysedlak/simple-multi-currency-for-woocommerce
 * Textdomain: simple-multi-currency-for-woocommerce
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0
 */

if (! defined( 'ABSPATH' ) ){ die('Plugin Simple multi-currency for Woocommerce by Invelity can not run !!!'); }
define('SMCFW_DIR_PATH',plugin_dir_path( __FILE__ ));
define('SMCFW_DIR_URL',plugin_dir_url( __FILE__ ));
define('SMCFW_PLUGIN_NAME',plugin_basename( __FILE__ ));
define('SMCFW_REPORTS_DIR', SMCFW_DIR_PATH.'includes/reports/');
define('SMCFW_ASSETS_URL', SMCFW_DIR_URL.'assets/');

if (!class_exists('InvelityPluginsAdmin')) {
    require_once(SMCFW_DIR_PATH.'includes/classes/class.invelityPluginsAdmin.php');
}
 if (!class_exists('SimpleMultiCurrencyForWoocommerce')) {
require_once(SMCFW_DIR_PATH.'includes/classes/class.SimpleMultiCurrencyForWoocommerce.php');
}
if (!class_exists('SimpleMultiCurrencyForWoocommerceProduct')) {
require_once(SMCFW_DIR_PATH.'includes/classes/class.SimpleMultiCurrencyForWoocommerce.Product.php');
}
new SimpleMultiCurrencyForWoocommerce();

include_once(SMCFW_DIR_PATH.'init.php');
include_once(SMCFW_DIR_PATH.'includes/functions/core.php');
include_once(SMCFW_DIR_PATH.'includes/functions/support.php');
include_once(SMCFW_DIR_PATH.'includes/functions/conversion.php');
include_once(SMCFW_DIR_PATH.'includes/functions/price.php');
include_once(SMCFW_DIR_PATH.'includes/functions/currency.php');
include_once(SMCFW_DIR_PATH.'includes/functions/fees.php');
include_once(SMCFW_DIR_PATH.'includes/functions/checkout.php');
include_once(SMCFW_DIR_PATH.'includes/functions/coupons.php');
include_once(SMCFW_DIR_PATH.'includes/functions/save-product.php');
include_once(SMCFW_DIR_PATH.'includes/functions/edit-product-page.php');
include_once(SMCFW_DIR_PATH.'includes/functions/edit-variation-product-page.php');
include_once(SMCFW_DIR_PATH.'includes/functions/schedule.php');
include_once(SMCFW_DIR_PATH.'includes/functions/switcher.php');
include_once(SMCFW_DIR_PATH.'includes/functions/filters.php');
include_once(SMCFW_DIR_PATH.'includes/functions/dashboard.php');
include_once(SMCFW_DIR_PATH.'includes/functions/shop-order.php');
include_once(SMCFW_DIR_PATH.'includes/functions/reports.php');
include_once(SMCFW_DIR_PATH.'includes/functions/payment.php');
include_once(SMCFW_DIR_PATH.'includes/functions/ajax-actions.php');
include_once(SMCFW_DIR_PATH.'includes/functions/orders-table-filter.php');
include_once(SMCFW_DIR_PATH.'includes/functions/logo.php');
include_once(SMCFW_DIR_PATH.'includes/functions/shortcodes.php');
include_once(SMCFW_DIR_PATH.'includes/functions/widgets.php');
include_once(SMCFW_DIR_PATH.'includes/admin/admin-fees-page.php');
include_once(SMCFW_DIR_PATH.'includes/admin/settings-page.php');
include_once(SMCFW_DIR_PATH.'includes/admin/products-table-admin.php');
include_once(SMCFW_DIR_PATH.'includes/admin/orders-table-admin.php');
include_once(SMCFW_DIR_PATH.'includes/admin/nav-menus.php');