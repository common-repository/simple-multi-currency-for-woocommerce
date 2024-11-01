<?php
add_action('admin_init', function(){
	if ( !class_exists( 'woocommerce' ) ) {
	add_action( 'admin_notices', function(){
	$class = 'notice notice-error';
	$message = __('Plugin Simple multi-currency for Woocommerce by Invelity was deactivated, because Woocommerce plugin is required to install.','simple-multi-currency-for-woocommerce');
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	});
	deactivate_plugins( plugin_basename( __FILE__ ) );
	}
});

add_action( 'plugins_loaded', 'smcfw_plugin_wc_init' );
function smcfw_plugin_wc_init() {
define('SMCFW_AJAX_URL',apply_filters('smcfw_filter_ajax_url',admin_url( 'admin-ajax.php' )));
define('SMCFW_SELF_UPDATES',apply_filters('smcfw_filter_self_updates',false));
}

function smcfw_plugin_activate() {
delete_option('smcfw_settings');
  if(!get_option('smcfw_settings')){ add_option('smcfw_settings',array('display_switcher'=>'on', 'rewrite_default_reports'=>'on', 'display_switcher_in_cart'=>'on', 'display_switcher_in_checkout'=>'on', 'display_switcher_in_woocommerce'=>'on', 'display_switcher_with_product_shorcode'=>'on', 'use_shipping_address'=>''));
	}
}
register_activation_hook( __FILE__, 'smcfw_plugin_activate' );

function smcfw_textdomain() {
load_plugin_textdomain( 'simple-multi-currency-for-woocommerce', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('init', 'smcfw_textdomain');


function smcfw_plugin_action_links( $links ) {
	$links = array_merge( array(
		'<img src="'.SMCFW_ASSETS_URL.'/images/icon-256x256.png" alt="" /><a style="color:#4DB435;" href="' . esc_url( admin_url( '/options-general.php?page=smcfw-general-settings-page' ) ) . '">' . __( 'Settings' ) . '</a>'
	), $links );
	return $links;
}
add_action( 'plugin_action_links_' . SMCFW_PLUGIN_NAME, 'smcfw_plugin_action_links' );