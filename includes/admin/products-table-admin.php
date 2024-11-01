<?php

remove_filter('manage_edit-product_columns', 'woocommerce_edit_order_columns');
add_filter('manage_edit-product_columns', 'smcfw_edit_product_columns');
add_action( 'manage_product_posts_custom_column', 'smcfw_woocommerce_values_product_columns', 2 );
add_filter( "manage_edit-product_sortable_columns", 'smcfw_woocommerce_sort_product' );


function smcfw_edit_product_columns($columns){
global $woocommerce;

  global $post;
    $product = wc_get_product($post->ID);
    unset($columns['price']);
    return array_slice( $columns, 0, 5, true )
	+ array( 'smcfw_price' => __('Price','woocommerce'))
	+ array_slice( $columns, 5, NULL, true );

}

function smcfw_woocommerce_values_product_columns($column){

    global $post;
      global $smcwf_settings;
    $settings = $smcwf_settings;
    $product = wc_get_product($post->ID);
    $id = $product->get_id();
  if ( $column == 'smcfw_price' ) { 

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
if(isset($id)){ $product_prices[$id][strtolower($shop_country)]=$product->get_regular_price(); }
	foreach($currencies as $k=>$c) {
		if($shop_country<>$k){
		$ks = strtolower($k);
		if(isset($id)){ 
			$pr = get_post_meta( $id, 'smcfw_regular_price_'.$ks, true );
			$product_prices[$id][$ks]=$pr;
		}
}
}


if(count($product_prices)>0){ foreach($product_prices[$id] as $lang=>$price){ 
	$cs = smcfw_get_country_currency(strtoupper($lang));
  $cd = smcfw_get_currency_code(strtoupper($lang));
    $setprice = smcfw_get_price($cs,$price);

    if($price==''){
      if(isset($settings['recalculate_currency_rates'])){
     print '<div>'.smcfw_get_flag($lang, '').' '. smcfw_get_price($cs,smcfw_convert($product->get_regular_price(),get_woocommerce_currency(),$cd)) . '</div>';
   }
    } else{
       print '<div>'.smcfw_get_flag($lang, '').' '. $setprice .'</div>';
}
} }
}

}

}

function smcfw_woocommerce_sort_product( $columns ) {
if(apply_filters('smcfw_filter_woocommerce_sort_product_bool',false)==false){ return $columns; }
    $custom = array(
        'smcfw_price'    => '_smcfw_price',
      );
    return wp_parse_args( $custom, $columns );
}

function smcfw_get_variation_regular_price( $product, $country_code, $min_or_max = 'min', $for_display = false ) {
  if(strtolower($country_code) == strtolower(smcfw_get_base_country())){
        $prices = $product->get_variation_prices( $for_display );
} else { 
  $childrens = $product->get_children();
  $prices = array();
  foreach($childrens as $chid){ $prices['regular_price'][]=get_post_meta($chid, 'smcfw_regular_price_'.$country_code, true ); }
}

//$price  = 'min' === $min_or_max ? current( $prices['regular_price'] ) : end( $prices['regular_price'] );
$price  = 'min' === $min_or_max ? min( $prices['regular_price'] ) : max( $prices['regular_price'] );
if(trim($price)==''){ $price = '<span class="na">–</span>'; } else{ 
$price = smcfw_get_price(smcfw_get_country_currency(strtoupper($country_code)), $price);
}
        return apply_filters( 'smcfw_filter_get_variation_regular_price', $price, $product, $min_or_max, $for_display );
}