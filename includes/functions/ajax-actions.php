<?php
add_action('wp_ajax_smcfwupdatefeesaction','smcfw_save_fees');

function smcfw_save_fees(){
check_ajax_referer( 'smcfwupdatefeesaction_nonce', 'security');
if(!current_user_can('manage_options')){ return; }
if(isset($_POST['gateway']) and is_array($_POST['gateway'])){
$arrg = array();
$gateway = smcfw_sanitize_array($_POST['gateway']);
if($gateway){ foreach($gateway as $key=>$value){ $arrg[$key]=$value; } }
if($arrg){ update_option( 'smcfw_gateways', $arrg ); }
} else{ update_option( 'smcfw_gateways', array() ); }

if(isset($_POST['currency']) and is_array($_POST['currency'])){
$arrc = array();
$currency = smcfw_sanitize_array($_POST['currency']);
if($currency){ foreach($currency as $key=>$value){ $arrc[$key]=$value; } }
if($arrc){ update_option( 'smcfw_currency', $arrc ); }
} else{ update_option( 'smcfw_currency', array() ); }

if(isset($_POST['freeshipping']) and is_array($_POST['freeshipping'])){
$arrs = array();
$freeshipping = smcfw_sanitize_array($_POST['freeshipping']);
if($freeshipping){ foreach($freeshipping as $key=>$value){ $arrs[$key]=$value; } }
if($arrs){ update_option( 'smcfw_freeshipping', $arrs ); }
} else{ update_option( 'smcfw_freeshipping', array() ); }

if(is_ajax()){ die(); }
}

add_action('wp_ajax_smcfwupdatesettingaction','smcfw_save_settings');

function smcfw_save_settings(){
check_ajax_referer( 'smcfwupdatesettingaction_nonce', 'security');
if(!current_user_can('manage_options')){ return; }

if(isset($_POST['settings']) and is_array($_POST['settings'])){
$arrs = array();
$settings = smcfw_sanitize_array($_POST['settings']);
if($settings){ foreach($settings as $key=>$value){ if($key=='use_shipping_address'){ global $woocommerce; 
	if(isset( WC()->customer )){ WC()->customer->set_shipping_country(WC()->customer->get_billing_country()); 
	} else{ WC()->customer->set_billing_country(WC()->customer->get_shipping_country()); } 
} $arrs[$key]=$value;  } }
if($arrs){ update_option( 'smcfw_settings', $arrs ); }
} else{ update_option( 'smcfw_settings', array() ); }

if(is_ajax()){ die(); }
}