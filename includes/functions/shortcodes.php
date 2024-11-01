<?php
// [smcfw_currency_switcher_shortcode]
add_shortcode( 'smcfw_currency_switcher_shortcode', function($atts){
do_action('smcfw_init_add_switcher');
});


// [smcfw_convert_price_shortcode price="10"]
function smcfw_convert_single_price_shortcode_func( $atts ) {
$country_code=smcfw_get_shipping_country();
if(!isset($atts['price'])){ $atts['price']=0; }
if(!isset($atts['from'])){ $atts['from']=get_woocommerce_currency(); }
if(!isset($atts['to'])){ $atts['to']=smcfw_get_currency_code($country_code); }
if(!isset($atts['symbol'])){ $atts['symbol']=smcfw_get_symbol_from_currency($atts['to']); }

$price = smcfw_convert($atts['price'], $atts['from'],$atts['to']);
return smcfw_get_price($atts['symbol'], $price);
}
add_shortcode( 'smcfw_convert_price_shortcode', 'smcfw_convert_single_price_shortcode_func' );

//[smcfw_product_prices_shortcode]
function smcfw_current_product_prices_shortcode_func( $atts ) {
global $post;
if(!isset($post)){ return ''; }
if($post->post_type != 'product'){ return ''; }
$product = wc_get_product($post->ID);
$id = $product->get_id();
$pricestring = '';
$product_prices = array();
$currencies = smcfw_get_allowed_countries();
$shop_country = smcfw_get_base_country();


if($product->is_type('variable')){
foreach($currencies as $k=>$c){
  $ks = strtolower($k);
  $min = smcfw_get_variation_regular_price($product, $ks, 'min');
  $max = smcfw_get_variation_regular_price($product, $ks, 'max');
  if($min==$max){ $setprice = $min; } else{ $setprice = $min . ' - ' . $max; }
print '<div>'.smcfw_get_flag($k, '').' '. $setprice .'</div>';
}
} elseif($product->is_type('grouped')){
print '<span class="na">–</span>';
}else {
if(isset($id)){ $product_prices[$id][strtolower($shop_country)]= get_post_meta( $id, '_regular_price', true ); /*$product->get_regular_price();*/ }
	foreach($currencies as $k=>$c) {
		if($shop_country<>$k){
		$ks = strtolower($k);
		if(isset($id)){ 
			$pr = get_post_meta( $id, 'smcfw_regular_price_'.$ks, true );
			$product_prices[$id][$ks]=$pr;
		}
}
}

if(count($product_prices)>0){ 
	foreach($product_prices[$id] as $lang=>$price){ 
	$cs = smcfw_get_country_currency(strtoupper($lang));
  $cd = smcfw_get_currency_code(strtoupper($lang));
    $setprice = smcfw_get_price($cs,$price);

    if($price==''){
      if(isset($settings['recalculate_currency_rates'])){
           $pricestring .= '<div>'.smcfw_get_flag($lang, '').' '. smcfw_get_price($cs,smcfw_convert($product->get_regular_price(),get_woocommerce_currency(),$cd)) . '</div>';
   }
    } else{
       $pricestring .= '<div class="item-'.$lang.'">'.smcfw_get_flag($lang, '').' '. $setprice .'</div>';
}
}
}

}

return $pricestring;
}

add_shortcode( 'smcfw_product_prices_shortcode', 'smcfw_current_product_prices_shortcode_func' );