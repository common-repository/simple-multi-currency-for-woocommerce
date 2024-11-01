<?php
/* save */
add_action('save_post_product', 'smcfw_save_simple_product_prices', 10, 3);

function smcfw_save_simple_product_prices( $post_id, $post, $update ) {
  if(!current_user_can('edit_post',$post_id)){ return; }
    $product = wc_get_product( $post_id );
  if(!$product->is_type( 'simple' )){ return; }
	if(!isset($_POST['smcfw_regular_price'])){ return; }
	if(!isset($_POST['smcfw_sale_price'])){ $sale_price = false; } else{ $sale_price = $_POST['smcfw_sale_price']; }
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ return; }
     // if our current user can't edit this post
    if( !current_user_can( 'edit_post', $post_id ) ) return;
    $sale_price = apply_filters('smcfw_filter_sale_price',$sale_price);
     $regular_price = apply_filters('smcfw_filter_regular_price',$_POST['smcfw_regular_price']);
if(!$regular_price){ return; }
  foreach($regular_price as $key=>$value){
        update_post_meta($post_id,'smcfw_regular_price_'.$key,sanitize_text_field($value));
        if($sale_price==false){ update_post_meta($post_id,'smcfw_sale_price_'.$key,''); }
  }

 if($sale_price<>false){ 
  	foreach($sale_price as $key=>$value){ update_post_meta($post_id,'smcfw_sale_price_'.$key,sanitize_text_field($value)); }
 }

 if(isset($_REQUEST['smcfw_sale_price_dates_from']) and count($_REQUEST['smcfw_sale_price_dates_from']>0)){
 	foreach($_REQUEST['smcfw_sale_price_dates_from'] as $key=>$value){ 
 		if(isset($sale_price[$key]) and trim($sale_price[$key])==''){ $value=''; }
 		if(isset($_REQUEST['smcfw_sale_price_dates_to'][$key]) and trim($_REQUEST['smcfw_sale_price_dates_to'][$key])==''){ $value=''; } 
 		update_post_meta($post_id,'smcfw_sale_price_dates_from_'.$key,sanitize_text_field($value)); }
 } else{ return; }

  if(isset($_REQUEST['smcfw_sale_price_dates_to']) and count($_REQUEST['smcfw_sale_price_dates_to']>0)){
 	foreach($_REQUEST['smcfw_sale_price_dates_to'] as $key=>$value){ 
 		if(isset($sale_price[$key]) and trim($sale_price[$key])==''){ $value=''; }
 		if(isset($_REQUEST['smcfw_sale_price_dates_from'][$key]) and trim($_REQUEST['smcfw_sale_price_dates_from'][$key])==''){ $value=''; }  
 		update_post_meta($post_id,'smcfw_sale_price_dates_to_'.$key,sanitize_text_field($value)); }
 } else{ return; }
   
}

function smcfw_save_variable_product_prices( $variation_id, $i ) {
if(!current_user_can('edit_post',$variation_id)){ return; } 
    if(!isset($_POST['smcfw_regular_price'][$variation_id])){ return; }
  if(!isset($_POST['smcfw_sale_price'][$variation_id])){ $sale_price = false; } else{ $sale_price = $_POST['smcfw_sale_price'][$variation_id]; }
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ return; }

    $sale_price = apply_filters('smcfw_filter_sale_price',$sale_price);
    $regular_price = apply_filters('smcfw_filter_regular_price',$_POST['smcfw_regular_price'][$variation_id]);
if(!$regular_price){ return; }
  foreach($regular_price as $key=>$value){
        update_post_meta($variation_id,'smcfw_regular_price_'.$key,sanitize_text_field($value));
        if($sale_price==false){ update_post_meta($variation_id,'smcfw_sale_price_'.$key,''); }
  }

 if($sale_price<>false){ 
    foreach($sale_price as $key=>$value){ update_post_meta($variation_id,'smcfw_sale_price_'.$key,sanitize_text_field($value)); }
 }

 if(!isset($_REQUEST['smcfw_sale_price_dates_from'][$variation_id])){ return; }
  $dates_from = $_REQUEST['smcfw_sale_price_dates_from'][$variation_id];

  foreach($dates_from as $key=>$value){ 
    if(isset($sale_price[$key]) and trim($sale_price[$key])==''){ $value=''; }
    update_post_meta($variation_id,'smcfw_sale_price_dates_from_'.$key,sanitize_text_field($value)); 
  }

 if(!isset($_REQUEST['smcfw_sale_price_dates_to'][$variation_id])){ return; }
  $dates_to = $_REQUEST['smcfw_sale_price_dates_to'][$variation_id];

  foreach($dates_to as $key=>$value){ 
    if(isset($sale_price[$key]) and trim($sale_price[$key])==''){ $value=''; }
    update_post_meta($variation_id,'smcfw_sale_price_dates_to_'.$key,sanitize_text_field($value)); 
  }

}
         
// add the action 
add_action( 'woocommerce_save_product_variation', 'smcfw_save_variable_product_prices', 10, 2 ); 