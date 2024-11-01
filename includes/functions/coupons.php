<?php
		define('E_WC_COUPON_INVALID_COUNTRY',99);
		add_action( 'woocommerce_coupon_options_usage_restriction', 'smcfw_coupon_options_data');
		add_action( 'woocommerce_coupon_options_save', 'smcfw_coupon_options_save' );
		add_action( 'woocommerce_coupon_loaded', 'smcfw_coupon_loaded' );
		add_filter( 'woocommerce_coupon_is_valid', 'smcfw_is_valid_for_country', 10, 2 );
		add_filter( 'woocommerce_coupon_error', 'smcfw_get_country_coupon_error', 10, 3 );

		add_filter( 'woocommerce_api_coupon_response', 'smcfw_coupon_response', 10, 2 );
		add_action( 'woocommerce_api_create_coupon', 'smcfw_create_coupon', 10, 2 );
		add_action( 'woocommerce_api_edit_coupon', 'smcfw_edit_coupon', 10, 2 );


	function smcfw_coupon_options_data() {
		global $post;
 
		// Billing Countries. ?>
		<div class="options_group">
		<p class="form-field"><label for="billing_countries"><?php _e( 'Billing countries', 'simple-multi-currency-for-woocommerce' ); ?></label>
		<select id="billing_countries" name="billing_countries[]" style="width: 50%;" class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any countries', 'simple-multi-currency-for-woocommerce' ); ?>">
			<?php
				$locations = (array) get_post_meta( $post->ID, 'billing_countries', true );
				$countries = WC()->countries->countries;

				if ( $countries ) foreach ( $countries as $key => $val ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( in_array( $key, $locations ), true, false ) . '>' . esc_html( $val ) . '</option>';
				}
			?>
		</select> <?php echo wc_help_tip( __( 'List of allowed countries to check against the customer\'s billing country for the coupon to remain valid.', 'simple-multi-currency-for-woocommerce' ) ); ?></p>
		<?php // Shipping Countries. ?>
		<p class="form-field"><label for="shipping_countries"><?php _e( 'Shipping countries', 'simple-multi-currency-for-woocommerce' ); ?></label>
		<select id="shipping_countries" name="shipping_countries[]" style="width: 50%;" class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any countries', 'simple-multi-currency-for-woocommerce' ); ?>">
			<?php
				$locations = (array) get_post_meta( $post->ID, 'shipping_countries', true );
				$countries = WC()->countries->countries;

				if ( $countries ) foreach ( $countries as $key => $val ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( in_array( $key, $locations ), true, false ) . '>' . esc_html( $val ) . '</option>';
				}
			?>
		</select> <?php echo wc_help_tip( __( 'List of allowed countries to check against the customer\'s shipping country for the coupon to remain valid.', 'simple-multi-currency-for-woocommerce' ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * Save coupons usage restriction meta box data.
	 */
	function smcfw_coupon_options_save( $post_id ) {
		if(!current_user_can('edit_post',$post_id)){ return; }
		$billing_countries  = isset( $_POST['billing_countries'] ) ? wc_clean( $_POST['billing_countries'] ) : array();
		$shipping_countries = isset( $_POST['shipping_countries'] ) ? wc_clean( $_POST['shipping_countries'] ) : array();

		// Save billing and shipping countries.
		update_post_meta( $post_id, 'billing_countries', $billing_countries );
		update_post_meta( $post_id, 'shipping_countries', $shipping_countries );
	}

	function smcfw_coupon_loaded( $coupon ) {
		$coupon->billing_countries  = get_post_meta( $coupon->get_id(), 'billing_countries', true );
		$coupon->shipping_countries = get_post_meta( $coupon->get_id(), 'shipping_countries', true );
	}

	function smcfw_is_valid_for_country( $valid_for_cart, $coupon ) {
		if ( sizeof( $coupon->billing_countries ) > 0 || sizeof( $coupon->shipping_countries ) > 0 ) {
			$valid_for_cart = false;
			if ( ! WC()->cart->is_empty() ) {
				if ( in_array( smcfw_get_shipping_country(), $coupon->billing_countries ) || in_array( smcfw_get_shipping_country(), $coupon->shipping_countries ) ) {
					$valid_for_cart = true;
				}
			}
			if ( ! $valid_for_cart ) {
				throw new Exception( E_WC_COUPON_INVALID_COUNTRY );
			}
		}

		return $valid_for_cart;
	}

	
	function smcfw_get_country_coupon_error( $err, $err_code, $coupon ) {
		if ( E_WC_COUPON_INVALID_COUNTRY == $err_code ) {
			$err = sprintf( __( 'Sorry, coupon "%s" is not applicable to your country.', 'simple-multi-currency-for-woocommerce' ), $coupon->get_code() );
		}

		return $err;
	}

	function smcfw_coupon_response( $coupon_data, $coupon ) {
		$coupon_data['billing_countries']  = $coupon->billing_countries;
		$coupon_data['shipping_countries'] = $coupon->shipping_countries;
		return $coupon_data;
	}

	function smcfw_create_coupon( $id, $data ) {
		if(!current_user_can('edit_post',$id)){ return; }
		$billing_countries  = isset( $data['billing_countries'] ) ? wc_clean( $data['billing_countries'] ) : array();
		$shipping_countries = isset( $data['shipping_countries'] ) ? wc_clean( $data['shipping_countries'] ) : array();

		// Save billing and shipping countries.
		update_post_meta( $id, 'billing_countries', $billing_countries );
		update_post_meta( $id, 'shipping_countries', $shipping_countries );
	}

	function smcfw_edit_coupon( $id, $data ) {
		if(!current_user_can('edit_post',$id)){ return; }
		if ( isset( $data['billing_countries'] ) ) {
			update_post_meta( $id, 'billing_countries', wc_clean( $data['billing_countries'] ) );
		}

		if ( isset( $data['shipping_countries'] ) ) {
			update_post_meta( $id, 'shipping_countries', wc_clean( $data['shipping_countries'] ) );
		}
	}



	add_filter( 'woocommerce_coupon_get_amount', 'smcfw_woocommerce_coupon_get_amount', 10, 2 );
	add_filter( 'woocommerce_coupon_get_minimum_amount', 'smcfw_woocommerce_coupon_get_minimum_amount' );
	add_filter( 'woocommerce_coupon_get_maximum_amount', 'smcfw_woocommerce_coupon_get_maximum_amount' );
	add_filter( 'woocommerce_boost_sales_coupon_amount_price', 'smcfw_woocommerce_boost_sales_coupon_amount_price');

	function smcfw_woocommerce_coupon_get_amount( $price, $coupon ) {
		if ( $coupon->is_type( array( 'percent' ) ) ) {
			return $price;
		}

		//if(smcfw_is_valid_for_country( false, $coupon )==true){ return $price;  }
if ( sizeof( $coupon->billing_countries ) > 0 || sizeof( $coupon->shipping_countries ) > 0 ) { return $price; }
		return smcfw_coupon_get_price( $price );
	}

	function smcfw_woocommerce_boost_sales_coupon_amount_price( $price ) {
		return smcfw_coupon_get_price( $price );
	}

	function smcfw_woocommerce_coupon_get_minimum_amount( $price ) {

		return smcfw_coupon_get_price( $price );
	}

	 function smcfw_woocommerce_coupon_get_maximum_amount( $price ) {
		return smcfw_coupon_get_price( $price );
	}


	function smcfw_coupon_get_price( $price, $currency_code = false ){
			if ( is_admin() && ! is_ajax() ) {
			return $price;
		}

$country_code=smcfw_get_shipping_country();
global $smcwf_settings;

if($country_code<>smcfw_get_base_country() and isset($smcwf_settings['recalculate_currency_rates'])){ 
$price = smcfw_convert($price, get_woocommerce_currency(),smcfw_get_currency_code($country_code)); 
}

		return $price;
	}