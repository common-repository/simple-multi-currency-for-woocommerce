<?php
remove_filter('manage_edit-shop_order_columns', 'woocommerce_edit_order_columns');
add_filter('manage_edit-shop_order_columns', 'smcfw_woocommerce_edit_order_columns');

function smcfw_woocommerce_edit_order_columns($columns){
global $woocommerce;
unset($columns['order_total']);
return array_merge($columns, 
              array(
              'smcfw_order_total' => __('Price','woocommerce'),
              ));

}

add_action( 'manage_shop_order_posts_custom_column', 'smcfw_woocommerce_order_columns_values', 2 );
function smcfw_woocommerce_order_columns_values($column){
    global $post;
    $currency = get_woocommerce_currency_symbol();
    $order = wc_get_order( $post->ID );
    $total = $order->get_total();
    $country = smcfw_get_order_shipping_country($order);
    $cs = smcfw_get_country_currency($country);
    $setprice = smcfw_get_price($cs,$total);
    if ( $column == 'smcfw_order_total' ) { echo apply_filters('smcfw_filter_order_total_column_price', $setprice, $setprice, $total, $country);  }
}

add_filter( "manage_edit-shop_order_sortable_columns", 'smcfw_woocommerce_orders_sort' );
function smcfw_woocommerce_orders_sort( $columns ) {
if(apply_filters('smcfw_filter_woocommerce_sort_shop_order_bool',false)==false){ return $columns; }
    $custom = array(
        'smcfw_order_total'    => '_smcfw_order_total',
    );
    return wp_parse_args( $custom, $columns );
}
