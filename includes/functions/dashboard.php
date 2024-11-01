<?php

		function smcfw_dashboard_init(){
		//remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
		    global $smcwf_settings;
    		$settings = $smcwf_settings;
			if(!isset($settings['rewrite_default_reports'])){ return; }
		wp_add_dashboard_widget( 'woocommerce_dashboard_status', __( 'WooCommerce status', 'woocommerce' ), 'smcfw_status_widget');

		}

		add_action( 'wp_dashboard_setup', 'smcfw_dashboard_init',1);


		function smcfw_get_sales_report_data() {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/reports/class-wc-report-sales-by-date.php';

			$sales_by_date                 = new WC_Report_Sales_By_Date();
			$sales_by_date->start_date     = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
			$sales_by_date->end_date       = current_time( 'timestamp' );
			$sales_by_date->chart_groupby  = 'day';
			$sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

			return $sales_by_date->get_report_data();
		}

		function smcfw_get_top_seller() {
			global $wpdb;

			$query            = array();
			$query['fields']  = "SELECT COUNT( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
			FROM {$wpdb->posts} as posts";
			$query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
			$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
			$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
			$query['where']   = "WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) ";
			$query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
			$query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
			$query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
			$query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "' ";
			$query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
			$query['groupby'] = 'GROUP BY product_id';
			$query['orderby'] = 'ORDER BY qty DESC';
			$query['limits']  = 'LIMIT 1';

			return $wpdb->get_row( implode( ' ', apply_filters( 'woocommerce_dashboard_status_widget_top_seller_query', $query ) ) );
		}

			/**
		 * Show order data is status widget.
		 */
		 function smcfw_status_widget_order_rows() {
			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				return;
			}
			$on_hold_count    = 0;
			$processing_count = 0;

			foreach ( wc_get_order_types( 'order-count' ) as $type ) {
				$counts            = (array) wp_count_posts( $type );
				$on_hold_count    += isset( $counts['wc-on-hold'] ) ? $counts['wc-on-hold'] : 0;
				$processing_count += isset( $counts['wc-processing'] ) ? $counts['wc-processing'] : 0;
			}
			?>
			<li class="processing-orders">
			<a href="<?php echo admin_url( 'edit.php?post_status=wc-processing&post_type=shop_order' ); ?>">
				<?php
					/* translators: %s: order count */
					printf(
						_n( '<strong>%s order</strong> awaiting processing', '<strong>%s orders</strong> awaiting processing', $processing_count, 'woocommerce' ),
						$processing_count
					);
				?>
				</a>
			</li>
			<li class="on-hold-orders">
				<a href="<?php echo admin_url( 'edit.php?post_status=wc-on-hold&post_type=shop_order' ); ?>">
				<?php
					/* translators: %s: order count */
					printf(
						_n( '<strong>%s order</strong> on-hold', '<strong>%s orders</strong> on-hold', $on_hold_count, 'woocommerce' ),
						$on_hold_count
					);
				?>
				</a>
			</li>
			<?php
		}

		/**
		 * Show stock data is status widget.
		 */
		function smcfw_status_widget_stock_rows() {
			global $wpdb;

			// Get products using a query - this is too advanced for get_posts :(
			$stock          = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
			$nostock        = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
			$transient_name = 'wc_low_stock_count';

			if ( false === ( $lowinstock_count = get_transient( $transient_name ) ) ) {
				$query_from       = apply_filters(
					'woocommerce_report_low_in_stock_query_from',
					"FROM {$wpdb->posts} as posts
					INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
					INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
					WHERE 1=1
					AND posts.post_type IN ( 'product', 'product_variation' )
					AND posts.post_status = 'publish'
					AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
					AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
					AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'"
				);
				$lowinstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );
				set_transient( $transient_name, $lowinstock_count, DAY_IN_SECONDS * 30 );
			}

			$transient_name = 'wc_outofstock_count';

			if ( false === ( $outofstock_count = get_transient( $transient_name ) ) ) {
				$query_from       = apply_filters(
					'woocommerce_report_out_of_stock_query_from',
					"FROM {$wpdb->posts} as posts
					INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
					INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
					WHERE 1=1
					AND posts.post_type IN ( 'product', 'product_variation' )
					AND posts.post_status = 'publish'
					AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
					AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$nostock}'"
				);
				$outofstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );
				set_transient( $transient_name, $outofstock_count, DAY_IN_SECONDS * 30 );
			}
			?>
			<li class="low-in-stock">
			<a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=stock&report=low_in_stock' ); ?>">
				<?php
					/* translators: %s: order count */
					printf(
						_n( '<strong>%s product</strong> low in stock', '<strong>%s products</strong> low in stock', $lowinstock_count, 'woocommerce' ),
						$lowinstock_count
					);
				?>
				</a>
			</li>
			<li class="out-of-stock">
				<a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=stock&report=out_of_stock' ); ?>">
				<?php
					/* translators: %s: order count */
					printf(
						_n( '<strong>%s product</strong> out of stock', '<strong>%s products</strong> out of stock', $outofstock_count, 'woocommerce' ),
						$outofstock_count
					);
				?>
				</a>
			</li>
			<?php
		}


		function smcfw_status_widget() {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/reports/class-wc-admin-report.php';
			global $woocommerce;
			$reports = new WC_Admin_Report();

			echo '<ul class="wc_status_list">';

			if ( current_user_can( 'view_woocommerce_reports' ) && ( $report_data = smcfw_get_sales_report_data() ) ) {
				$countries = smcfw_get_allowed_countries();
				$ssales = smcfw_get_country_sales();
				//print_r($ssales);
				//print_r($countries);
				foreach($countries as $countrycode=>$country){
				?>
				<li class="sales-this-month">
				<a onclick="if(this.getAttribute('href')=='#'){ return false; }" href="<?php 
				echo apply_filters('smcfw_filter_widget_sales_this_month_url','admin.php?page=wc-reports&tab=orders&range=month',$countrycode); ?>">
					<?php print $country; //echo $reports->sales_sparkline( '', max( 7, date( 'd', current_time( 'timestamp' ) ) ) ); ?>
					<?php
						/* translators: %s: net sales */
						//$report_data->net_sales;
						if(isset($ssales[$countrycode]) and trim($ssales[$countrycode])<>''){ $psPrice = $ssales[$countrycode]; } else{ $psPrice = 0; }
						printf(
							__( '%s net sales this month', 'woocommerce' ),
							'<strong>' . smcfw_get_price(smcfw_get_country_currency($countrycode), apply_filters('iwc_filter_sanitize_price',$psPrice)) . '</strong>'
						);
						
						?>
					</a>
				</li>
				<?php }
			}

			if ( current_user_can( 'view_woocommerce_reports' ) && ( $top_seller = smcfw_get_top_seller() ) && $top_seller->qty ) {
				?>
				<li class="best-seller-this-month">
				<a href="<?php //print '#'; 
echo admin_url('edit.php?s&post_status=all&post_type=shop_order&action=-1&m='.date('Ym').'&order_table_filter_product_order_product_filter='.$top_seller->product_id.'&order_table_filter_product_order_shipping_filter=all&_customer_user&filter_action=Filtrova%C5%A5&paged=1&action2=-1');
				//echo admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product&range=month&product_ids=' . $top_seller->product_id ); ?>">
					<?php echo $reports->sales_sparkline( $top_seller->product_id, max( 7, date( 'd', current_time( 'timestamp' ) ) ), 'count' ); ?>
					<?php
						/* translators: 1: top seller product title 2: top seller quantity */
						printf(
							__( '%1$s top seller this month (sold %2$d)', 'woocommerce' ),
							'<strong>' . get_the_title( $top_seller->product_id ) . '</strong>',
							$top_seller->qty
						);
						?>
					</a>
				</li>
				<?php
			}
			smcfw_status_widget_order_rows();
			smcfw_status_widget_stock_rows();

			do_action( 'woocommerce_after_dashboard_status_widget', $reports );
			echo '</ul>';
		}

add_filter('iwc_filter_sanitize_price',function($price){ if($price==''){ return 0; } else{ return $price; }});


function smcfw_get_country_sales() {
        global $wpdb;

$start_date=date('Y-m-d',strtotime("first day of this month"));
$enddate=date('Y-m-d',strtotime("last day of this month"));
$end_date=date('Y-m-d',strtotime($enddate.'+ 1 day'));
$start_date=apply_filters('smcfw_filter_get_country_sales_country_start_date',$start_date,$start_date);
$end_date=apply_filters('smcfw_filter_get_country_sales_country_end_date',$end_date,$end_date);
$shipping_country = apply_filters('smcfw_filter_get_country_sales_country','_billing_country');
$post_statuses_default = array('wc-processing','wc-on-hold','wc-completed');
$post_statuses_default = apply_filters('smcfw_filter_get_country_sales_post_statuses',$post_statuses_default,$post_statuses_default);
$post_statuses = array();
foreach($post_statuses_default as $status){ $post_statuses[]="'".$status."'"; }
$post_statuses_string  = implode(',', $post_statuses);
        $sql = "SELECT country.meta_value AS country_name,
                SUM(order_item_meta.meta_value) AS sale_total
                FROM {$wpdb->prefix}woocommerce_order_items AS order_items

                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta
                    ON order_items.order_item_id = order_item_meta.order_item_id
                LEFT JOIN {$wpdb->postmeta} AS country
                    ON order_items.order_id = country.post_id
                LEFT JOIN {$wpdb->posts} AS posts
                    ON order_items.order_id = posts.ID

                WHERE posts.post_type             = 'shop_order'
                AND   country.meta_key            = '$shipping_country'
                AND   order_item_meta.meta_key    = '_line_total'
                AND   order_items.order_item_type = 'line_item'
                AND   posts.post_date            >= '$start_date'
                AND   posts.post_date            < '$end_date'
                AND   posts.post_status IN ($post_statuses_string)
                GROUP BY country.meta_value";

        $saless = $wpdb->get_results( $sql );
$arr = array();
			      foreach ( $saless as $view ) {
                    $arr[$view->country_name]=$view->sale_total;
                   }
         return $arr;
    }