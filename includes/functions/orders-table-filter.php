<?php 
add_action( 'restrict_manage_posts', 'smcfw_order_table_filter_product_product_filter_in_order');
		
function smcfw_order_table_filter_product_product_filter_in_order(){

		global $typenow, $wpdb;

		if ( 'shop_order' != $typenow ) {
			return;
		}

		$st = '';
		$all_posts = array();
	    $sql="SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish'".$st;
		$sql = apply_filters('smcfw_filter_order_table_filter_product_product_filter_in_order_sql',$sql,$sql);
		$all_posts = $wpdb->get_results($sql, ARRAY_A);
		$values = array();
		$all_posts = apply_filters('smcfw_filter_order_table_filter_product_product_filter_in_order_array', $all_posts, $all_posts);
		if(!$all_posts){ return; }
		foreach ($all_posts as $all_post) {
		$title = get_the_title($all_post['ID']);
		$values[$title] = $all_post['ID'];
		}
	    ?>
	    <span id="order_table_filter_product_order_product_filter_wrap">
		    <select name="order_table_filter_product_order_product_filter" id="order_table_filter_product_order_product_filter">
		    <option value="all"><?php _e('All Products','woocommerce'); ?></option>
		    <?php
		        $current_v = isset($_GET['order_table_filter_product_order_product_filter'])? sanitize_text_field($_GET['order_table_filter_product_order_product_filter']):'';
		        foreach ($values as $label => $value) {
		            printf
		                (
		                    '<option value="%s"%s>%s</option>',
		                    $value,
		                    $value == $current_v ? ' selected="selected"':'',
		                    $label
		                );
		            }
		    ?>
		    </select>
		</span>
		
	    <script type="text/javascript">
			jQuery('#order_table_filter_product_order_product_filter').select2();
	   </script>
	    <?php
	}

add_action( 'restrict_manage_posts', 'smcfw_order_table_filter_product_shipping_filter_in_order');
		
function smcfw_order_table_filter_product_shipping_filter_in_order(){

		global $typenow, $wpdb;

		if ( 'shop_order' != $typenow ) {
			return;
		}

$all_posts = array();
$countries = smcfw_get_allowed_countries();
$countries = apply_filters('smcfw_filter_order_table_filter_product_shipping_filter_in_order_countries',$countries,$countries);

if(!$countries){ return; }

foreach($countries as $country_code=>$country_title){
	$all_posts[$country_code] = array('ID'=>$country_code, 'post_title'=>$country_title);
}

$all_posts = apply_filters('smcfw_filter_order_table_filter_product_shipping_filter_in_order_array', $all_posts, $all_posts);

if(!$all_posts){ return; }

		$values = array();
		if(!$all_posts){ return; }
		foreach ($all_posts as $all_post) {
		$title = apply_filters('get_the_title',$all_post['post_title']);
		$values[$title] = $all_post['ID'];
		}
	    ?>
	    <span id="order_table_filter_product_order_shipping_filter_wrap">
		    <select name="order_table_filter_product_order_shipping_filter" id="order_table_filter_product_order_shipping_filter">
		    <option value="all"><?php _e('Select a country&hellip;','woocommerce'); ?></option>
		    <?php
		        $current_v = isset($_GET['order_table_filter_product_order_shipping_filter'])? sanitize_text_field($_GET['order_table_filter_product_order_shipping_filter']):'';
		        foreach ($values as $label => $value) {
		            printf
		                (
		                    '<option value="%s"%s>%s</option>',
		                    $value,
		                    $value == $current_v ? ' selected="selected"':'',
		                    $label
		                );
		            }
		    ?>
		    </select>
    
		</span>
		
	    <script type="text/javascript">
			jQuery('#order_table_filter_product_order_shipping_filter').select2();
	   </script>
	    <?php
	}	
	
add_action( 'posts_where', 'smcfw_order_table_filter_product_product_filter_where' );

	function smcfw_order_table_filter_product_product_filter_where( $where ) {
		if(is_admin()){
		if( is_search() ) {
			global $typenow, $wpdb;

		if ( 'shop_order' != $typenow ) {
			return $where;
		}
   $where = apply_filters('smcfw_filter_order_table_filter_product_product_filter_where',$where);
	}
	}
	
		//print '<div style="bottom:0px; padding:5px; border:1px red solid; max-width:500px; margin-left:calc(50% - 250px); color:red; background: white; position:fixed; z-index: 999999999;">'.apply_filters('smcfw_filter_order_table_filter_product_product_filter_where',$where).'</div>';
		return apply_filters('smcfw_filter_order_table_filter_product_product_filter_where',$where);
	}

add_filter('smcfw_filter_order_table_filter_product_product_filter_where', 'smcfw_order_table_filter_product_product_filter_where_byproduct');
function smcfw_order_table_filter_product_product_filter_where_byproduct($where){
			global $wpdb;
			$clear_where = $where;
			$t_posts = $wpdb->posts;
			$t_order_items = $wpdb->prefix . "woocommerce_order_items";  
			$t_order_itemmeta = $wpdb->prefix . "woocommerce_order_itemmeta";

			if ( isset( $_GET['order_table_filter_product_order_product_filter'] ) && !empty( $_GET['order_table_filter_product_order_product_filter'] ) && trim( $_GET['order_table_filter_product_order_product_filter'] ) <> 'all' ) {
				$product = intval($_GET['order_table_filter_product_order_product_filter']);
				$product = sanitize_text_field($product);
				$where = $clear_where . " AND $product IN (SELECT $t_order_itemmeta.meta_value FROM $t_order_items LEFT JOIN $t_order_itemmeta on $t_order_itemmeta.order_item_id=$t_order_items.order_item_id WHERE $t_order_items.order_item_type='line_item' AND $t_order_itemmeta.meta_key='_product_id' AND $t_posts.ID=$t_order_items.order_id)";
			}
return apply_filters('smcfw_filter_order_table_filter_product_product_filter_where_byproduct',$where);
} 

add_filter('smcfw_filter_order_table_filter_product_product_filter_where', 'smcfw_order_table_filter_product_product_filter_where_byshippingcountry');
function smcfw_order_table_filter_product_product_filter_where_byshippingcountry($where){
			global $wpdb;
			$clear_where = $where;
			$t_posts = $wpdb->posts;
			$t_postmeta = $wpdb->postmeta;

			if ( isset( $_GET['order_table_filter_product_order_shipping_filter'] ) && !empty( $_GET['order_table_filter_product_order_shipping_filter'] ) && trim( $_GET['order_table_filter_product_order_shipping_filter'] ) <> 'all' ) {
				$country = (string) $_GET['order_table_filter_product_order_shipping_filter'];
				$country = sanitize_text_field($country);
				$where = $clear_where . " AND $t_posts.ID IN (SELECT $t_postmeta.post_id FROM $t_postmeta WHERE $t_postmeta.meta_key = '".apply_filters('smcfw_filter_get_country_sales_country','_billing_country')."' AND $t_postmeta.meta_value = '$country')";
			}
return apply_filters('smcfw_filter_order_table_filter_product_product_filter_where_byshippingcountry',$where);
}