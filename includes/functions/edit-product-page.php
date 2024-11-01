<?php
add_action( 'woocommerce_product_options_pricing', 'smcfw_add_price_fields_to_product' );
function smcfw_add_price_fields_to_product(){
	global $woocommerce;
	global $post;
	$currencies = smcfw_get_allowed_countries();
	$shop_country = smcfw_get_base_country();
	do_action( 'smcfw_action_add_price_fields_to_product_start');
	foreach($currencies as $k=>$c) {
		if($shop_country<>$k){
	do_action('smcfw_action_add_price_fields_to_product_loop_item_start',$k,$c);
	do_action('smcfw_action_add_price_fields_to_product_loop_item',$k,$c);
	do_action('smcfw_action_add_price_fields_to_product_loop_item_end',$k,$c);
	}
	}
	do_action( 'smcfw_action_add_price_fields_to_product_end');
}

add_action('smcfw_action_add_price_fields_to_product_loop_item', 'smcfw_add_price_fields_to_product_loop_item_func', 10, 2);


function smcfw_add_price_fields_to_product_loop_item_func($country_code,$country_title){ 
$k = $country_code;
$c = $country_title;
$ks = strtolower($k);
global $woocommerce;
global $post;
  
		if($post){ 
			$pr = get_post_meta( $post->ID, 'smcfw_regular_price_'.$ks, true );
			$ps = get_post_meta( $post->ID, 'smcfw_sale_price_'.$ks, true );
		}

		if(intval($pr) or doubleval($pr)){ $regular_price = $pr; } else{ $regular_price='';}
		if(intval($ps) or doubleval($ps)){ $sale_price = $ps; } else{ $sale_price='';}

	echo '<div class="smcfw-inputs-container">';
  woocommerce_wp_text_input( array(
        'id'        => '_regular_price_'.$ks,
        'value'     => $regular_price,
        'label'     => __('Regular price','woocommerce') . ' ('.$c.')',
        'data_type' => 'price',
		'class'			=> 'smcfw_regular_price_input',
        'name'      => 'smcfw_regular_price['.$ks.']',
        'description'=> ''
      ) );

  woocommerce_wp_text_input( array(
        'id'        => '_sale_price_'.$ks,
        'value'     => $sale_price,
        'label'     => __('Sale price','woocommerce') . ' ('.$c.')',
        'data_type' => 'price',
        'class'			=> 'smcfw_sale_price_input',
        'name'      => 'smcfw_sale_price['.$ks.']',
        'description'=> '<a href=".sale_price_dates_fields_'.$ks.'" class="smcfw-simple-product smcfw_sale_schedule sale_schedule_'.$ks.'" style="display: inline;">' . __( 'Schedule', 'woocommerce' ) . '</a>'
      ) );
	echo '</div>';		
$sale_price_dates_from = '';
if(!is_null(get_post_meta($post->ID,'smcfw_sale_price_dates_from_'.$ks,true))){ $sale_price_dates_from = get_post_meta($post->ID,'smcfw_sale_price_dates_from_'.$ks,true); }
$sale_price_dates_to = '';
if(!is_null(get_post_meta($post->ID,'smcfw_sale_price_dates_to_'.$ks,true))){ $sale_price_dates_to = get_post_meta($post->ID,'smcfw_sale_price_dates_to_'.$ks,true); }
if($sale_price_dates_from=='' or $sale_price_dates_to=='' or $sale_price=='' or $sale_price==0){ $ddd = 'display:none;'; } else{ $ddd='display:block;'; }
		echo '<p style="'.$ddd.'" class="smcfw-sale-price-dates-fields form-field sale_price_dates_fields_'.$ks.'">
				<label for="_sale_price_dates_from_'.$ks.'">' . esc_html__( 'Sale price dates', 'woocommerce' ) . ' ('.$c.')</label>
				<input style="float:none; margin-bottom:1em;" type="text" class="short datepicker datepicker-from" name="smcfw_sale_price_dates_from['.$ks.']" id="_sale_price_dates_from_'.$ks.'" value="' . esc_attr( $sale_price_dates_from ) . '" placeholder="' . esc_html( _x( 'From&hellip;', 'placeholder', 'woocommerce' ) ) . ' YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
				<input style="float:none; margin-bottom:1em;" type="text" class="short datepicker datepicker-to" name="smcfw_sale_price_dates_to['.$ks.']" id="_sale_price_dates_to_'.$ks.'" value="' . esc_attr( $sale_price_dates_to ) . '" placeholder="' . esc_html( _x( 'To&hellip;', 'placeholder', 'woocommerce' ) ) . '  YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
				<a href="#" class="smcfw_cancel_sale_schedule smcfw-simple-product description cancel_sale_schedule_'.$ks.'">' . esc_html__( 'Cancel', 'woocommerce' ) . '</a>' . wc_help_tip( __( 'The sale will end at the beginning of the set date.', 'woocommerce' ) ) . '
			</p>';
	$datpicsettings = array('from'=>'#_sale_price_dates_from_'.$ks, 'to'=>'#_sale_price_dates_to_'.$ks, 'id'=>$ks, 'format'=>'yy-mm-dd');
	do_action('smcfw_action_js_datepicker_inputs',$datpicsettings);

}