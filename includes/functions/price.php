<?php
function smcfw_custom_price($type = 'regular_price',$price, $product) {

  global $preprice;
  $preprice = $price; //save original price for future use
      // Delete product cached price  (if needed)
    wc_delete_product_transients($product->get_id());

  if(is_admin() and !is_ajax()){ return apply_filters('smcfw_filter_custom_price',$price); } //dont use in admin page
       global $woocommerce;
       global $smcfw_country;
       global $allow_smcfw_country;
       $smcfw_country = smcfw_get_shipping_country();
       if(smcfw_get_base_country()==$smcfw_country){  return floatval(apply_filters('smcfw_filter_custom_price',$price,$preprice)); } //if shipping country is same as woocommerce shop country
      
       $p = get_post_meta( $product->get_id(), 'smcfw_'.$type.'_'.strtolower($smcfw_country), true );
       if(isset($p) and $p<>0 and trim($p)<>''){ $price = $p; $allow_smcfw_country=true; } else{ $allow_smcfw_country = false; $price=''; }

if(apply_filters('smcfw_filter_custom_price_allow',true)==false){ $allow_smcfw_country = false; $price = apply_filters('smcfw_filter_preprice',$preprice); }

      return floatval(apply_filters('smcfw_filter_custom_price',$price,$preprice));
}

function smcfw_wc_get_price( $sale_price, $product ) { //cena so zlavou
  if($product->is_on_sale()){ return smcfw_custom_price('sale_price', $sale_price, $product); }
  return smcfw_custom_price('regular_price', $sale_price, $product);
}
add_filter( 'woocommerce_product_get_price', 'smcfw_wc_get_price', 50, 2 );

function smcfw_wc_get_sale_price( $sale_price, $product ) {
  return smcfw_custom_price('sale_price',$sale_price, $product);
}
add_filter( 'woocommerce_product_get_sale_price', 'smcfw_wc_get_sale_price', 50, 2 );

function smcfw_wc_get_regular_price( $sale_price, $product ) { //povodna cena
   return smcfw_custom_price('regular_price',$sale_price, $product);
}
add_filter( 'woocommerce_product_get_regular_price', 'smcfw_wc_get_regular_price', 50, 2 );

// Variable
add_filter('woocommerce_product_variation_get_regular_price', 'smcfw_get_variation_regular_prices', 99, 2 );
add_filter('woocommerce_product_variation_get_price', 'smcfw_get_variation_sale_prices' , 99, 2 );

function smcfw_get_variation_regular_prices( $price, $product ) {
	$preprice = $price;
wc_delete_product_transients($product->get_id());
$price = smcfw_wc_get_regular_price( $price, $product );
    return apply_filters('smcfw_filter_variation_price',$price,$preprice);
}

function smcfw_get_variation_sale_prices($price, $product){
$preprice = $price;
  wc_delete_product_transients($product->get_id());
  if(smcfw_get_shipping_country()<>smcfw_get_base_country()){
  if($product->is_on_sale()){ return apply_filters('smcfw_filter_variation_price',get_post_meta( $product->get_id(), 'smcfw_sale_price_'.strtolower(smcfw_get_shipping_country()), true ),$preprice); }
   return apply_filters('smcfw_filter_variation_price',get_post_meta( $product->get_id(), 'smcfw_regular_price_'.strtolower(smcfw_get_shipping_country()), true ),$preprice);
 }
    return apply_filters('smcfw_filter_variation_price',$price,$preprice);
}

// Variations (of a variable product)
add_filter('woocommerce_variation_prices_price', 'smcfw_get_variation_prices', 99, 3 );
add_filter('woocommerce_variation_prices_regular_price', 'smcfw_get_variation_prices', 99, 3 );

function smcfw_get_variation_prices( $price, $variation, $product ) {
	$preprice = $price;
  wc_delete_product_transients($variation->get_id());
 if(smcfw_get_shipping_country()<>smcfw_get_base_country()){
  if($variation->is_on_sale()){ 
    
    $p = get_post_meta( $variation->get_id(), 'smcfw_sale_price_'.strtolower(smcfw_get_shipping_country()), true ); 
    if(isset($p)){ return apply_filters('smcfw_filter_variation_price',$p,$preprice); } else { return ''; }
  }
  
   $s = get_post_meta( $variation->get_id(), 'smcfw_regular_price_'.strtolower(smcfw_get_shipping_country()), true );
    if(isset($s)){ return apply_filters('smcfw_filter_variation_price',$s,$preprice); } else { return ''; }
 }
     return apply_filters('smcfw_filter_variation_price',$price,$preprice);
}


add_filter( 'woocommerce_format_sale_price', 'smcfw_format_sale_price', 20, 3 );
function smcfw_format_sale_price( $price, $regular_price, $sale_price ) {
  /* Deprecated since version 1.0.10 */
/* if(apply_filters('smcfw_filter_format_sale_price_bool',false)==true){ 
 if(floatval($sale_price)>0){
return apply_filters('smcfw_filter_format_sale_price',wc_price($sale_price), $price, $regular_price, $sale_price);
}
} */
return $price;
}