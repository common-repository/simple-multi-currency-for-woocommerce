<?php 
function smcfw_convert($price, $from='EUR',$to='USD'){
   global $smcwf_settings;
    $settings = $smcwf_settings;
    if(!isset($settings['recalculate_currency_rates'])){ return floatval($price); }

	$rates = array();
  $shopc = get_woocommerce_currency();
try {

  if(apply_filters('smcfw_filter_convert_always_load_rates',false)==true){
    delete_transient('smcfw_currency_rates');
  }
    if(!get_transient('smcfw_currency_rates')){
    $url = file_get_contents('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
	$xml =  new SimpleXMLElement($url) ;
	foreach($xml->Cube->Cube->Cube as $rate){ $rates[strval($rate["currency"])]= strval($rate["rate"]); }
    set_transient( 'smcfw_currency_rates', $rates, 12 * HOUR_IN_SECONDS );
    } else{ $rates = get_transient('smcfw_currency_rates'); }
if(count($rates)==0){ return floatval($price); }
if(!array_key_exists($to, $rates) and $to<>$shopc){ return floatval($price); }
  $rates[strval($shopc)]=strval(1);

  if($to==$shopc){ $base = floatval(strval($rates[$from])); $prep=$base; } else{  $base = floatval(strval($rates[$to])); $prep = 1 / $base;}
 $returnprice = floatval(strval(floatval($price) / $prep));
 return apply_filters('smcfw_convert_returnprice',round($returnprice,2),$returnprice);
} catch (Exception $e) { return floatval($price); }
}

add_filter('smcfw_filter_custom_price','smcfw_filter_prices',10,2);
add_filter('smcfw_filter_variation_price','smcfw_filter_prices',10,2); 

function smcfw_filter_prices($price=0,$preprice=0){
    global $smcwf_settings;
    $settings = $smcwf_settings;
    if(!isset($settings['recalculate_currency_rates'])){ return floatval($price); }
    global $woocommerce;
    if(floatval($price) !== ''){ return floatval($price); }
    $from = get_woocommerce_currency();
    $to = smcfw_get_currency_code(smcfw_get_shipping_country());
    if($from == $to){ return $preprice; }
    $convert = smcfw_convert($preprice, $from,$to);
    if($convert==false){ return $preprice; } else{ return $convert; }
}