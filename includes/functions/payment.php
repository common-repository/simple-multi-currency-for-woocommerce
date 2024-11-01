<?php
function smcfw_payment_paypal($currency_code){
	$bool = true;
	$allowed = array();
	return apply_filters('smcfw_filter_payment_paypal',$bool);
}