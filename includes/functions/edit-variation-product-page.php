<?php //html-variation-admin.php
add_action( 'woocommerce_variation_options_pricing', 'smcfw_add_price_fields_to_variation_product',10,3);

function smcfw_add_price_fields_to_variation_product($loop, $variation_data, $variation){
	global $woocommerce;
	global $post;
	$currencies = smcfw_get_allowed_countries();
	$shop_country = smcfw_get_base_country();
	do_action( 'smcfw_action_add_price_fields_to_variation_product_start',$loop, $variation_data, $variation);
	foreach($currencies as $k=>$c) {
		if($shop_country<>$k){
	do_action('smcfw_action_add_price_fields_to_variation_product_loop_item_start',$k,$c,$loop,$variation_data,$variation);
	do_action('smcfw_action_add_price_fields_to_variation_product_loop_item',$k,$c,$loop,$variation_data,$variation);
	do_action('smcfw_action_add_price_fields_to_variation_product_loop_item_end',$k,$c,$loop,$variation_data,$variation);
	}
	}
	do_action( 'smcfw_action_add_price_fields_to_variation_product_end',$loop, $variation_data, $variation);
}

add_action('smcfw_action_add_price_fields_to_variation_product_loop_item', 'smcfw_add_price_fields_to_variation_product_loop_item_func', 10, 5);

function smcfw_add_price_fields_to_variation_product_loop_item_func($country_code, $country_title, $loop, $variation_data, $variation){
	$ks = strtolower($country_code);
	$variation_object = wc_get_product($variation->ID);
	$loop = $variation->ID;

		if($variation){ 
			$pr = get_post_meta( $variation->ID, 'smcfw_regular_price_'.$ks, true );
			$ps = get_post_meta( $variation->ID, 'smcfw_sale_price_'.$ks, true );
		}

		if(intval($pr) or doubleval($pr)){ $regular_price = $pr; } else{ $regular_price='';}
		if(intval($ps) or doubleval($ps)){ $sale_price = $ps; } else{ $sale_price='';}

	echo '<div class="smcfw-inputs-container">';
				$label = sprintf(
					/* translators: %s: currency symbol */
					__( 'Regular price (%s)', 'woocommerce' ),
					$country_title
				);

				woocommerce_wp_text_input(
					array(
						'id'            => "smcfw_variable_regular_price_{$loop}_{$ks}",
						'name'          => "smcfw_regular_price[{$loop}][{$ks}]",
						'value'         => wc_format_localized_price( $regular_price ),
						'label'         => $label,
						'class'			=> 'smcfw_regular_price_input',
						'data_type'     => 'price',
						'wrapper_class' => 'form-row form-row-first',
						'placeholder'   => __( 'Variation price (required)', 'woocommerce' ),
					)
				);


				$label = sprintf(
					/* translators: %s: currency symbol */
					__( 'Sale price (%s)', 'woocommerce' ),
					$country_title
				);

				woocommerce_wp_text_input(
					array(
						'id'            => "smcfw_variable_sale_price{$loop}_{$ks}",
						'name'          => "smcfw_sale_price[{$loop}][{$ks}]",
						'value'         => wc_format_localized_price( $sale_price ),
						'data_type'     => 'price',
						'class'			=> 'smcfw_sale_price_input',
						'label'         => $label . ' <a href=".sale_price_dates_fields_'.$ks.'_'.$loop.'" class="smcfw_sale_schedule smcfw-variation-product">' . esc_html__( 'Schedule', 'woocommerce' ) . '</a><a href=".sale_price_dates_fields_'.$ks.'_'.$loop.'" class="smcfw_cancel_sale_schedule smcfw-variation-product hidden">' . esc_html__( 'Cancel schedule', 'woocommerce' ) . '</a>',
						'wrapper_class' => 'form-row form-row-last',
					)
				);
echo '</div>';

$sale_price_dates_from = '';
if(!is_null(get_post_meta($variation->ID,'smcfw_sale_price_dates_from_'.$ks,true))){ $sale_price_dates_from = get_post_meta($variation->ID,'smcfw_sale_price_dates_from_'.$ks,true); }
$sale_price_dates_to = '';
if(!is_null(get_post_meta($variation->ID,'smcfw_sale_price_dates_to_'.$ks,true))){ $sale_price_dates_to = get_post_meta($variation->ID,'smcfw_sale_price_dates_to_'.$ks,true); }
if($sale_price_dates_from=='' or $sale_price_dates_to=='' or $sale_price=='' or $sale_price==0){ $ddd = 'display:none;'; } else{ $ddd='display:block;'; }

								echo '<div style="'.$ddd.'" class="smcfw-sale-price-dates-fields form-field sale_price_dates_fields_'.$ks.'_'.$loop.' Xhidden">
					<p class="form-row form-row-first">
						<label>' . __( 'Sale start date', 'woocommerce' ) . '('.esc_attr($country_title).')</label>
						<input id="_sale_price_dates_from_'.$loop.'_'.$ks.'" type="text" class="smcfw_sale_price_dates_from" name="smcfw_sale_price_dates_from['.$loop.']['.$ks.']" value="' . esc_attr( $sale_price_dates_from ) . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'woocommerce' ) . ' YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
					</p>
					<p class="form-row form-row-last">
						<label>' . __( 'Sale end date', 'woocommerce' ) . '('.esc_attr($country_title).')</label>
						<input id="_sale_price_dates_to_'.$loop.'_'.$ks.'" type="text" class="smcfw_sale_price_dates_to" name="smcfw_sale_price_dates_to['.$loop.']['.$ks.']" value="' . esc_attr( $sale_price_dates_to ) . '" placeholder="' . esc_html_x( 'To&hellip;', 'placeholder', 'woocommerce' ) . '  YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
					</p>
				</div>';
					$datpicsettings = array('from'=>'#_sale_price_dates_from_'.$loop.'_'.$ks, 'to'=>'#_sale_price_dates_to_'.$loop.'_'.$ks, 'id'=>$loop.$ks, 'format'=>'yy-mm-dd');
	do_action('smcfw_action_js_datepicker_inputs',$datpicsettings);
}