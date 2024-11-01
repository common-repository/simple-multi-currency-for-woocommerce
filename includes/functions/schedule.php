<?php
add_filter( 'woocommerce_product_is_on_sale', 'smcfw_product_is_on_sale', 10, 2 );
function smcfw_product_is_on_sale( $on_sale, $product ){
$ks = smcfw_get_shipping_country();
if($ks == smcfw_get_base_country()){ return $on_sale; }
//if($product->is_type('variable') || $product->is_type('variation')){ return false; }
#overenie ak ide o inu krajinu ako default woocommerce
$ks = strtolower($ks);
$on_sale = false;
$sale_price_dates_from = '';
if(!is_null(get_post_meta($product->get_id(),'smcfw_sale_price_dates_from_'.$ks,true))){ $sale_price_dates_from = get_post_meta($product->get_id(),'smcfw_sale_price_dates_from_'.$ks,true); } else{ $on_sale = false; }
if(trim($sale_price_dates_from)==''){ $on_sale = false; }
$sale_price_dates_to = '';
if(!is_null(get_post_meta($product->get_id(),'smcfw_sale_price_dates_to_'.$ks,true))){ $sale_price_dates_to = get_post_meta($product->get_id(),'smcfw_sale_price_dates_to_'.$ks,true); } else{ $on_sale = false; }
if(trim($sale_price_dates_to)==''){ $on_sale = false; }

 $now = new DateTime();
    $startdate = new DateTime($sale_price_dates_from);
    $enddate = new DateTime($sale_price_dates_to);

    if($startdate <= $now && $now <= $enddate) {
        $on_sale = true;
    }else{
        $on_sale = false;
    }

$nowf = $now->format('Y-m-d');
$startdatef=$startdate->format('Y-m-d');
$enddatef=$enddate->format('Y-m-d');

    if($startdatef==$nowf){ $on_sale=true; }
    if($enddatef==$nowf){ $on_sale=true; }

if($product->is_type('variable') || $product->is_type('variation')){
    $sale_price = get_post_meta( $product->get_id(), 'smcfw_sale_price_'.strtolower(smcfw_get_shipping_country()), true );

if(('' == (string) $sale_price or $sale_price == 0) and $on_sale == true){ $on_sale = false; }
} else {
if(('' == (string) $product->get_sale_price() or $product->get_sale_price() == 0) and $on_sale == true){ $on_sale = false; }
}
return apply_filters('smcfw_filter_product_is_on_sale',$on_sale, $on_sale, $product);
}