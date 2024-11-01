<?php
function smcfw_get_shipping_country(){ //function to set country from variable 'smcfw_change_currency'
global $woocommerce;
if( !isset( WC()->customer ) ){ smcfw_set_shipping_country_directly(); /*return apply_filters('smcfw_filter_country_code',smcfw_get_base_country());*/ }
if(null == smcfw_wc_customer_get_country(WC()->customer)){ smcfw_set_shipping_country_directly(); /*return apply_filters('smcfw_filter_country_code',smcfw_get_base_country());*/ }
$country = apply_filters('smcfw_filter_get_shipping_country_get_country',smcfw_wc_customer_get_country(WC()->customer));	
if(isset($_REQUEST['smcfw_change_currency'])){ $country = sanitize_text_field($_REQUEST['smcfw_change_currency']); }
$country = strtoupper($country);
smcfw_set_shipping_country(apply_filters('smcfw_filter_change_currency',esc_attr($country)));
return apply_filters('smcfw_filter_country_code',$country);
}

function smcfw_get_current_gateway() {
		global $woocommerce;

		$available_gateways = smcfw_get_payment_gateways();
		$current_gateway = null;
		$default_gateway = get_option( 'woocommerce_default_gateway' );
		if ( ! empty( $available_gateways ) ) {

		   // Chosen Method
			if ( isset( WC()->session->chosen_payment_method ) && isset( $available_gateways[ WC()->session->chosen_payment_method ] ) ) {
				$current_gateway = $available_gateways[ WC()->session->chosen_payment_method ];
			} elseif ( isset( $available_gateways[ $default_gateway ] ) ) {
				$current_gateway = $available_gateways[ $default_gateway ];
			} else {
				$current_gateway = current( $available_gateways );
			}
		}
		if ( ! is_null( $current_gateway ) ){
			$current_gateway = $current_gateway;
		} else {
			$current_gateway = false;
		}
		return apply_filters('smcfw_filter_get_current_gateway',$current_gateway);
}

function smcfw_get_flag($lang, $lang_title = ''){
if(!isset($lang)){ return ''; }
$img = '<img style="height: 16px; width: auto;" class="smcfw-flag-icon" src="'.SMCFW_DIR_URL.'assets/flags/'.strtolower($lang).'.svg" alt="'.$lang_title.'">';
return apply_filters('smcfw_filter_get_flag',$img);
}

function smcfw_set_shipping_country_directly(){
	global $woocommerce;

	if(is_ajax() or is_admin()){ return; }
	if( !isset( WC()->customer ) ){ return; }
	if(is_null(WC()->customer)){ return; }
		if(is_null(smcfw_wc_customer_get_country(WC()->customer))){ smcfw_set_shipping_country(smcfw_get_base_country()); }
}

function smcfw_wc_customer_get_country($customer){
if(is_null($customer)){ return $customer; }
global $woocommerce;
if(apply_filters('smcfw_filter_get_shipping_country_allow',false)==true){
return $customer->get_shipping_country();
} else{ return $customer->get_billing_country(); }
}

function smcfw_wc_customer_set_country($customer, $country_code){
if(is_null($customer)){ return $country_code; }
global $woocommerce;
if(apply_filters('smcfw_filter_get_shipping_country_allow',false)==true){
return $customer->set_shipping_country($country_code);
} else{ return $customer->set_billing_country($country_code); }
}

function smcfw_set_shipping_country($country_code){
global $woocommerce;
$code = apply_filters('smcfw_filter_set_shipping_country',$country_code);
if(is_null(WC()->customer)){ return $code; }
smcfw_wc_customer_set_country(WC()->customer, $code);
return $code;
}

function smcfw_get_base_country(){
global $woocommerce;
$country = WC()->countries->get_base_country();
return apply_filters('smcfw_filter_get_base_country',$country);
}

function smcfw_get_allowed_countries(){
global $woocommerce;
$countries = WC()->countries->get_allowed_countries();
return apply_filters('smcfw_filter_get_allowed_countries',$countries);
}

function smcfw_get_payment_gateways(){
		global $woocommerce;
		$available_gateways = WC()->payment_gateways->payment_gateways();
		return apply_filters('smcfw_filter_get_payment_gateways',$available_gateways);
}

function smcfw_get_available_payment_gateways(){
		global $woocommerce;
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
		return apply_filters('smcfw_filter_get_available_payment_gateways',$available_gateways);
}

function smcfw_get_gateways(){
$arr = get_option( 'smcfw_gateways' );
return apply_filters('smcfw_filter_get_gateways',$arr);
}

function smcfw_get_curency(){
$arr = get_option( 'smcfw_currency' );
return apply_filters('smcfw_filter_get_currency',$arr);
}

function smcfw_get_freeshipping(){
$arr = get_option( 'smcfw_freeshipping' );
return apply_filters('smcfw_filter_get_freeshipping',$arr);
}

function smcfw_sanitize_array($array){
$tags = $array;
return apply_filters('smcfw_filter_sanitize_array',$tags);
}

function smcfw_get_settings(){
$arr = get_option('smcfw_settings');
return apply_filters('smcfw_filter_smcfw_settings',$arr);
}

function smcfw_get_price($currency_symbol='', $price=0){
	global $woocommerce;
	if($currency_symbol==get_woocommerce_currency_symbol()){ return wc_price($price); }
	$s = sprintf( get_woocommerce_price_format(), $currency_symbol, $price);
	return apply_filters('smcfw_filter_get_price',$s);
}

function smcfw_get_order_shipping_country($order){
/* Allowed since version 1.0.11 */
if(apply_filters('smcfw_filter_get_shipping_country_allow',false)==true){
	if(trim($order->get_shipping_country())<>''){ $country = $order->get_shipping_country(); } else{ $country = $order->get_billing_country(); }
} else{ 
 	if(trim($order->get_billing_country())<>''){ $country = $order->get_billing_country(); } else{ $country = $order->get_shipping_country(); }	
}
/* Deprecated since version 1.0.11 */
//if(trim($order->get_shipping_country())<>''){ $country = $order->get_shipping_country(); } else{ $country = $order->get_billing_country(); }
return apply_filters('smcfw_filter_get_order_shipping_country_return',$country);
}

