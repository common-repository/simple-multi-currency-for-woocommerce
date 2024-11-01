<?php
function smcfw_shop_order_set_currency( $post_id, $post ) {

// If post is a revision, don't send the email.
if ( wp_is_post_revision( $post_id ) ){
    return;
}

if($post->post_type<>'shop_order'){ return; }

if(!current_user_can('edit_post',$post_id)){ return; }

$order = new WC_Order( $post_id );
$country_code = smcfw_get_order_shipping_country($order);
$currencies = smcfw_get_currencies_array();
$currency_code = smcfw_get_currency_code($country_code);
//$order->set_currency('CZK');
update_post_meta( $post_id, '_order_currency', sanitize_text_field($currency_code));
}
add_action( 'wp_insert_post', 'smcfw_shop_order_set_currency', 10, 2 );
//add_action( 'save_post', 'smcfw_shop_order_set_currency', 10, 2 );


add_filter('woocommerce_get_formatted_order_total','smcfw_get_formatted_order_total',10,4);


     function smcfw_get_formatted_order_total( $formatted_total, $order, $tax_display, $display_refunded ) {
      	$currency = get_post_meta( $order->get_id(), '_order_currency', true );
		$symbol = smcfw_get_symbol_from_currency($currency);
		$formatted_total = smcfw_get_price($symbol, $order->get_total());
        $order_total     = $order->get_total();
        $total_refunded  = $order->get_total_refunded();
        $tax_string      = '';
//smcfw_get_price($symbol, $price=0)
        // Tax for inclusive prices.
        if ( wc_tax_enabled() && 'incl' === $tax_display ) {
            $tax_string_array = array();
            $tax_totals       = $order->get_tax_totals();

            if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                foreach ( $tax_totals as $code => $tax ) {
                    $tax_amount         = ( $total_refunded && $display_refunded ) ? smcfw_get_price($symbol, WC_Tax::round( $tax->amount - $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) ) : $tax->formatted_amount;
                    $tax_string_array[] = sprintf( '%s %s', $tax_amount, $tax->label );
                }
            } elseif ( ! empty( $tax_totals ) ) {
                $tax_amount         = ( $total_refunded && $display_refunded ) ? $order->get_total_tax() - $order->get_total_tax_refunded() : $order->get_total_tax();
                $tax_string_array[] = sprintf( '%s %s', smcfw_get_price($symbol, $tax_amount), WC()->countries->tax_or_vat() );
            }

            if ( ! empty( $tax_string_array ) ) {
                /* translators: %s: taxes */
                $tax_string = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) ) . '</small>';
            }
        }

        if ( $total_refunded && $display_refunded ) {
            $formatted_total = '<del>' . strip_tags( $formatted_total ) . '</del> <ins>' . smcfw_get_price($symbol, $order_total - $total_refunded ) . $tax_string . '</ins>';
        } else {
            $formatted_total .= $tax_string;
        }

        /**
         * Filter WooCommerce formatted order total.
         *
         * @param string   $formatted_total  Total to display.
         * @param WC_Order $order            Order data.
         * @param string   $tax_display      Type of tax display.
         * @param bool     $display_refunded If should include refunded value.
         */
        return apply_filters( 'smcfw_filter_woocommerce_get_formatted_order_total', $formatted_total, $order, $tax_display, $display_refunded );;
    }