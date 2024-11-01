<?php //https://docs.woocommerce.com/wc-apidocs/source-class-WC_Admin_Reports.html#26-37

add_action('admin_init',function(){
    global $smcwf_settings;
    $settings = $smcwf_settings;
if(!isset($settings['rewrite_default_reports'])){ return; }
add_filter( 'woocommerce_admin_reports', 'smcfw_woocommerce_admin_reports');
add_filter( 'wc_admin_reports_path', 'smcfw_admin_reports_path',10,2);
add_filter('smcfw_filter_report_orders_line_color','smcfw_filter_report_orders_line_color_func',10,2);
add_action('admin_enqueue_scripts','smcfw_reports_enqueue');
});

function smcfw_woocommerce_admin_reports($reports){
    $wc_admin_reports = new WC_Admin_Reports();
    $reports['orders']= array(
                'title'  => __( 'Orders', 'woocommerce' ),
                'reports' => array(
                    'sales_by_date' => array(
                        'title'       => __( 'Sales by date', 'woocommerce' ),
                        'description' => '',
                        'hide_title'  => true,
                        'callback'    => array( $wc_admin_reports, 'get_report' ),
                    ),
                ),
            );
    return apply_filters('smcfw_filter_woocommerce_admin_reports',$reports);
}

function smcfw_admin_reports_path($name,$class){ 
switch ($class) {
    case 'sales-by-date':
        $name = SMCFW_REPORTS_DIR . 'orders.php';
        break;
    default:
       $name = apply_filters('smcfw_filter_admin_reports_path_default',$name,$class);
}

return apply_filters('smcfw_filter_admin_reports_path_return',$name,$class);
}

function smcfw_filter_report_orders_line_color_func($color, $i){
    $i = (int) $i;
    $defaultcolors = apply_filters('smcfw_filter_filter_report_orders_line_color_func_colors',array('#3498db', '#8fdece', 'orange', 'blue', 'red', 'green', 'pink', 'purple', 'lime'));
    if(isset($defaultcolors[$i])){ $color = $defaultcolors[$i]; } else{ $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); }
    return $color;
}

function smcfw_reports_enqueue(){
$screen = get_current_screen();
if($screen->id=='woocommerce_page_wc-reports'){
wp_enqueue_script('chart-js', SMCFW_ASSETS_URL . 'js/chart.js', array('jquery'), '', false);
}
}