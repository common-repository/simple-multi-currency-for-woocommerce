<?php
function smcfw_init($content){
global $smcwf_settings;
global $smcfw_shortcode_products;
if(isset($smcwf_settings['display_switcher_in_cart']) and is_cart()){ do_action('smcfw_init_add_switcher'); return; }
if(isset($smcwf_settings['display_switcher_in_shop']) and is_shop()){ do_action('smcfw_init_add_shop'); return; }
if(isset($smcwf_settings['display_switcher_in_checkout']) and is_checkout()){ do_action('smcfw_init_add_switcher'); return; }
if(isset($smcwf_settings['display_switcher_in_woocommerce']) and is_woocommerce()){ do_action('smcfw_init_add_switcher'); return; }
if(isset($smcwf_settings['display_switcher_with_product_shorcode']) and $smcfw_shortcode_products==true){ do_action('smcfw_init_add_switcher'); return; }
// Deprecated  since version 1.0.11
//if(is_cart() or is_shop() or is_woocommerce() or is_checkout() or $smcfw_shortcode_products==true){ do_action('smcfw_init_add_switcher'); }
}

add_action('wp_footer','smcfw_init');

add_action('smcfw_init_add_switcher','smcfw_init_add_switcher_callback');

function smcfw_init_add_switcher_callback(){
	if(is_admin()){ return; }
print apply_filters('smcfw_filter_init_add_switcher_callback_css','
<style>
	#language-selector-div{ position:fixed !important;  right:0px; top:40%; z-index:9999; padding:5px; padding-top:0px; background-color: white; width:auto; }
	#language-selector-div a{ display:block;  }
	#language-selector-div a.grayscale{ filter:grayscale(100); }
	#language-selector-div a.nograyscale{ filter:grayscale(0); }
	#language-selector-div a:hover{
	-moz-transition: all .5s ease-in;
    -ms-transition: all .5s ease-in;
    -o-transition: all .5s ease-in;
    transition: all .5s ease-in;
    filter:grayscale(0);
	}
</style>');
print '<div id="language-selector-div" class="shadow">';
do_action('smcfw_display_switcher');
print '</div>';
}

add_action('smcfw_display_switcher','smcfw_create_display_switcher');
function smcfw_create_display_switcher(){
	?>
	<?php $arr=smcfw_get_allowed_countries();
	if(isset($arr)){
		if(count($arr)>1){
$absolute_url = smcfw_full_url( $_SERVER );
foreach($arr as $key=>$title){ 
	$uk = 'smcfw_change_currency='.$key;
	$absolute_url=str_replace($uk,'',strval($absolute_url));
 	$absolute_url=str_replace('&','a',strval($absolute_url));
 	$absolute_url=str_replace('a#038;','',strval($absolute_url));
}
//echo $absolute_url; die();
foreach($arr as $key=>$title){ ?>
	<a class="<?php 
$sc = smcfw_get_shipping_country();
echo $key.'-smcfw-flag-switch smcfw-flags-switch ';
if($sc <> ''){
	if($key==$sc){ print 'nograyscale'; } else{ print 'grayscale'; }
} else{
	if($key==smcfw_get_base_country()){print 'nograyscale'; } else{ print 'grayscale';}
}

if(strpos($absolute_url, '?') !== false){  
	$aburl = $absolute_url . '&smcfw_change_currency='.$key; 
} else{ 
	$aburl = $absolute_url . '?smcfw_change_currency='.$key; 
}
?>" title="<?php print $title; ?>" currency="<?php echo $key; ?>" href="<?php echo $aburl; ?>"><span class="shadow"><?php print smcfw_get_flag($key, $title); ?></span></a>
<?php } } } ?>
<script>
	<?php if(apply_filters('smcfw_filter_get_shipping_country_allow',false)==true){ ?>
	jQuery(document).ready(function($) {
		$('body').on('change', '[name=shipping_country]', function(event) {
			event.preventDefault();
			/* Act on the event */
var sel = $(this).val();
 $('.smcfw-flags-switch').removeClass('nograyscale').addClass('grayscale');
 $('.' + sel + '-smcfw-flag-switch').addClass('nograyscale');
		});
	});
<?php } else { ?>
		jQuery(document).ready(function($) {
		$('body').on('change', '[name=billing_country]', function(event) {
			event.preventDefault();
			/* Act on the event */
var sel = $(this).val();
 $('.smcfw-flags-switch').removeClass('nograyscale').addClass('grayscale');
 $('.' + sel + '-smcfw-flag-switch').addClass('nograyscale');
		});
	});
<?php } ?>
</script>
<?php
}

function smcfw_prefix_detect_shortcode()
{
	global $smcfw_shortcode_products;
    global $wp_query;	
    $posts = $wp_query->posts;
    $pattern = get_shortcode_regex();
    $smcfw_shortcode_products = false;
    if(isset($posts) and !is_admin()){
    foreach ($posts as $post){
		if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
			&& array_key_exists( 2, $matches )
			&& in_array( 'products', $matches[2] ) )
		{
			// enque my css and js
			$smcfw_shortcode_products = true;
			break;	
		}    
    }
}
}
add_action( 'wp', 'smcfw_prefix_detect_shortcode' );

function smcwf_url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function smcfw_full_url( $s, $use_forwarded_host = false )
{
	$url = smcwf_url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
	return esc_url($url);
}