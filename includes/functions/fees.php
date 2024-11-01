<?php
function smcfw_woo_add_cart_fee() {
  global $woocommerce;
  global $smcfw_country;
  $smcfw_country = smcfw_get_shipping_country();
$smcfw_country = apply_filters('smcfw_filter_woo_add_cart_fee_country',$smcfw_country);
$cena = 0;
$dobierka=smcfw_get_gateways();
$cg = smcfw_get_current_gateway();
if(isset($dobierka[$cg->id][$smcfw_country])){
$cena = $dobierka[$cg->id][$smcfw_country]['amount']; 
$title = $dobierka[$cg->id][$smcfw_country]['title'];
$percent = $dobierka[$cg->id][$smcfw_country]['percent'];
} else { $cena = 0; $title = ''; $percent = 0; }
$packages = WC()->shipping->get_packages();
$wc_shipping_price = WC()->cart->shipping_total;
$default_cena = $dobierka[$cg->id]['default']['amount'];
$default_percent = $dobierka[$cg->id]['default']['percent'];

if($cena == 0 and $default_cena<>0){
	$cena  = $default_cena;
}

if($percent == 0 and $default_percent<>0){
	$percent  = $default_percent;
}

$freeshipping = smcfw_get_freeshipping();

foreach ($packages as $i => $package) {
$chosen_method = isset(WC()->session->chosen_shipping_methods[$i]) ? WC()->session->chosen_shipping_methods[$i] : '';
$cm = explode(':',$chosen_method);

if((isset($freeshipping[$cg->id]['free']) and $cm[0] == 'free_shipping') or (isset($freeshipping[$cg->id]['zero']) and $wc_shipping_price==0)){
smcfw_add_fee('', 0, 0 );
} else{ smcfw_add_fee($title, $cena, $percent); }
//}
}
}

add_action( 'woocommerce_cart_calculate_fees', 'smcfw_woo_add_cart_fee' );

function smcfw_add_fee($title, $price, $percent = 0){
	global $woocommerce;
	if($percent > 0){
	$price = (( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) / 100) * $percent; 
	}
	$woocommerce->cart->add_fee($title, $price );
}