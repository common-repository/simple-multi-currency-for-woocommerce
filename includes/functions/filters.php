<?php

add_filter( 'woocommerce_admin_disabled', function(){ 
global $smcwf_settings;
$smcwf_settings = smcfw_get_settings();
/* Disable Woocommerce Admin in Woocommerce */
if(isset($smcwf_settings['rewrite_default_reports'])){ return true; }
});

add_action('init','smcfw_remove_actions');
function smcfw_remove_actions(){
if(is_admin()){ return; }
global $smcwf_settings;
$settings = $smcwf_settings;
if(!isset($settings)){ return; }
if(!isset($settings['display_switcher'])){
	remove_action('smcfw_init_add_switcher','smcfw_init_add_switcher_callback');
}
if(isset($settings['wpml_support'])){ 
			add_filter('smcfw_filter_get_shipping_country','smcfw_wpml',10,1);
			remove_action('smcfw_init_add_switcher','smcfw_init_add_switcher_callback');
}

if(isset($settings['polylang_support'])){ add_filter('smcfw_filter_get_shipping_country','smcfw_polylang',10,1); }
if(isset($settings['qtranslate_support'])){ add_filter('smcfw_filter_get_shipping_country','smcfw_qtranslate',10,1); }
if(isset($settings['qtranslate_x_support'])){ add_filter('smcfw_filter_get_shipping_country','smcfw_qtranslate_x',10,1); }
/* Allowed since version 1.0.11 */
if(isset($settings['use_shipping_address']) and trim($settings['use_shipping_address'])<>''){ add_filter('smcfw_filter_get_shipping_country_allow',function(){ return true; },10,1); }
}

/* Deprecated since version 1.0.11 */
// hide coupon field on cart page
function smcfw_hide_coupon_field_on_cart( $enabled ) {
	if ( is_cart() ) {
		$enabled = apply_filters('smcfw_filter_hide_coupon_field_on_cart_bool',true); 
	}
	return apply_filters('smcfw_filter_hide_coupon_field_on_cart',$enabled);
}
//add_filter( 'woocommerce_coupons_enabled', 'smcfw_hide_coupon_field_on_cart' );
 
// hide coupon field on checkout page
function smcfw_hide_coupon_field_on_checkout( $enabled ) {
	if ( is_checkout() ) {
		$enabled = apply_filters('smcfw_filter_hide_coupon_field_on_checkout_bool',true);
	}
	return apply_filters('smcfw_filter_hide_coupon_field_on_checkout',$enabled);
}
//add_filter( 'woocommerce_coupons_enabled', 'smcfw_hide_coupon_field_on_checkout' );

// hide shipping calculator on cart page
function smcfw_woocommerce_remove_shipping_calculator($needs_shipping) {
  if (is_cart()) {
    return apply_filters('smcfw_filter_woocommerce_remove_shipping_calculator_bool',false);
  }
  return apply_filters('smcfw_filter_woocommerce_remove_shipping_calculator',$needs_shipping);
}
//add_filter( 'woocommerce_cart_needs_shipping', 'smcfw_woocommerce_remove_shipping_calculator', 10, 1);