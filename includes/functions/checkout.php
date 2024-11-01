<?php
function smcfw_hide_shipping_when_free_is_available( $rates, $package ) {
	$new_rates = array();
	foreach ( $rates as $rate_id => $rate ) {
		// Only modify rates if free_shipping is present.
		if ( 'free_shipping' === $rate->method_id ) {
			$new_rates[ $rate_id ] = $rate;
			break;
		}
	}

	if ( ! empty( $new_rates ) ) {
		//Save local pickup if it's present.
		foreach ( $rates as $rate_id => $rate ) {
			if ('local_pickup' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}
		return $new_rates;
	}

	return $rates;
}

add_filter( 'woocommerce_package_rates', 'smcfw_hide_shipping_when_free_is_available', 10, 2 );

add_action( 'woocommerce_review_order_before_payment', 'smcfw_refresh_checkout_on_payment_methods_change' );
 
function smcfw_refresh_checkout_on_payment_methods_change(){
    ?>
    <script type="text/javascript">
        (function($){
            $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                $('body').trigger('update_checkout');
            });
        })(jQuery);
    </script>
    <?php
}

add_filter( 'woocommerce_gateway_method_title', 'smcfw_woocommerce_gateway_method_title', 10, 2);

function smcfw_woocommerce_gateway_method_title($method_title, $gateway){
if(!is_checkout()){ return $method_title; }
$available_gateways = smcfw_get_gateways();
$method_title_single = '';
$method_title_default = '';
if(isset($available_gateways[$gateway->id][smcfw_get_shipping_country()]['title'])){ $method_title_single=$available_gateways[$gateway->id][smcfw_get_shipping_country()]['title']; }
if(isset($available_gateways[$gateway->id]['default']['title'])){ $method_title_default=$available_gateways[$gateway->id]['default']['title']; }
if(''!==trim($method_title_single)){ $method_title = $method_title_default; }
if(''!==trim($method_title_single)){ $method_title = $method_title_single; }
return apply_filters('smcfw_filter_woocommerce_gateway_method_title',$method_title,$gateway);
}

// set selected country in checkout page
add_filter( 'default_checkout_billing_country', 'smcfw_change_default_checkout_country' );
 function smcfw_change_default_checkout_country($country) { return smcfw_get_shipping_country();
// if(apply_filters('smcfw_filter_get_shipping_country_allow',false)==true){ return $country; } else {  }
}