<?php
function smcfw_init_support(){
			// Declare theme support for features.
if(apply_filters('smcfw_filter_init_support_wc_product_gallery_zoom',true)==true){ add_theme_support( 'wc-product-gallery-zoom' ); }

if(apply_filters('smcfw_filter_init_support_wc_product_gallery_lightbox',true)==true){ add_theme_support( 'wc-product-gallery-lightbox' ); }

if(apply_filters('smcfw_filter_init_support_wc_product_gallery_slider',true)==true){ add_theme_support( 'wc-product-gallery-slider' ); }

if(apply_filters('smcfw_filter_init_support_woocommerce',true)==false){
	add_theme_support( 'woocommerce', array(
	'thumbnail_image_width' => apply_filters('smcfw_filter_init_support_woocommerce_thumbnail_image_width',300),
	'single_image_width'    => apply_filters('smcfw_filter_init_support_woocommerce_single_image_width',600),
		) );
}
}

add_action('init','smcfw_init_support');

function smcfw_wpml($content){
if(is_checkout()){ return $content; }
if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
  $content = esc_attr(ICL_LANGUAGE_CODE);
}
return apply_filters('smcfw_sanitize_country_code',$content);
}

add_filter('smcfw_sanitize_country_code', function($content){
if(trim($content)=='cs'){
$content=str_replace('cs', 'cz', $content); 
}
return apply_filters('smcfw_filter_sanitize_country_code',$content);
});

function smcfw_polylang($content){
if ( function_exists( 'pll_current_language' ) ) {
  $content = esc_attr(pll_current_language('slug'));
}
return $content;
}

function smcfw_qtranslate($content){
	if(function_exists('qtrans_getLanguage')){
		$content = qtrans_getLanguage();
	}
return $content;
}

function smcfw_qtranslate_x($content){
	if(function_exists('qtranxf_getLanguage')){
		$content = qtranxf_getLanguage();
	}
return $content;
}