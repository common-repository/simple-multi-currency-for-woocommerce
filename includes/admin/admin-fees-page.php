<?php
add_action( 'admin_menu', 'smcfw_fees_page_menu' );

function smcfw_fees_page_menu() {
	global $smcfw_plugin_admin_page;
	$smcfw_plugin_admin_page = add_submenu_page( 'woocommerce', __('Payment settings','simple-multi-currency-for-woocommerce'), '<span style="margin-right:5px; color:#4DB435;" class="dashicons dashicons-cart"></span><b style="color:#4DB435;">'.__('Payment settings','simple-multi-currency-for-woocommerce').'</b><div style="font-size:xx-small;">Simple multi-currency for Woocommerce by <svg style="width:50px; fill:#fff;" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 162 23" xml:space="preserve"><path d="M0,23h6.7V0H0V23z"></path>
    <path d="M9.9,23h6.7V10.9L25.1,23h5.7V0h-6.7v12.2L15.5,0H9.9V23z"></path>
    <path d="M42.1,23h7.1l8.8-23h-7l-5.3,15.2L40.2,0h-7L42.1,23z"></path>
    <path d="M60.4,23h17.3v-5.1H67v-3.8h9.3V8.9H67V5.1h10.6V0H60.4V23z"></path>
    <path d="M79.8,23h17v-5.1H86.5V0h-6.7V23z"></path>
    <path d="M125.1,23h6.7V5h6.4V0h-19.5v5h6.4V23z"></path>
    <path d="M147.1,23h6.7v-8.4L162,0h-7l-4.6,8.7L145.8,0h-7l8.2,14.6V23z"></path>
    <path fill="#e23b73" class="highlight" d="M97.7,23h6.8l13.2-22.9h-6.8L97.7,23z"></path>
</svg></div>', 'manage_options', 'smcfw-setting-page', 'smcfw_fees_page_func' );
}


function smcfw_fees_page_func(){
if(!is_ajax() and isset($_POST['security'])){smcfw_save_fees();}
global $woocommerce;
$currencies = smcfw_get_allowed_countries();
$available_gateways = smcfw_get_available_payment_gateways();
$arr = smcfw_get_gateways();
$mena = smcfw_get_curency();
$freeshipping = smcfw_get_freeshipping();
global $smcwf_settings;
$settings = $smcwf_settings;
?>
<style>
    .wp-core-ui .button-success, .wp-core-ui .button-success:hover, .wp-core-ui .button-success:active, .wp-core-ui .button-success:focus {
    background: #5cb85c;
    border-color: #4cae4c;
    box-shadow: 0 1px 0 #4cae4c;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 -1px 1px #4cae4c, 1px 0 1px #4cae4c, 0 1px 1px #4cae4c, -1px 0 1px #4cae4c;
}
.wp-core-ui .button-danger, .wp-core-ui .button-danger:hover, .wp-core-ui .button-danger:active, .wp-core-ui .button-danger:focus {
    background: #c9302c;
    border-color: #ac2925;
    box-shadow: 0 1px 0 #ac2925;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 -1px 1px #ac2925, 1px 0 1px #ac2925, 0 1px 1px #ac2925, -1px 0 1px #ac2925;
}
.tab-hide {
    display: none;
}
#setform {
    margin-top: 1em;
}
#setform nav {
    margin-bottom: 1em;
}
#setform [type="number"] {
    width: 80px;
}
.div-tabs, .button-div-footer {
    max-width: 800px;
    margin: auto;
}
.button-div-footer {
    text-align: left;
}
</style>
<form id="setform" action="" method="post">
<h2><?php _e('Payment settings','simple-multi-currency-for-woocommerce'); ?></h2>
    <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
<a href="#tab-general" class="smcfw-nav-tab nav-tab nav-tab-active"><?php _e('General');
    ?></a>
<?php foreach($available_gateways as $key=>$object) {
    ?>
<a href="#tab-<?php print $key; ?>" class="smcfw-nav-tab nav-tab"><?php print $object->get_method_title(); /*print $object->title;*/ ?></a>
<?php
}
?>
</nav>
<?php foreach($available_gateways as $key=>$object) {
    ?>
    <div class="div-tabs tab-hide" id="tab-<?php print $key;
    ?>">
   <table>
<tr><th><?php _e('Country', 'woocommerce');
    ?></th><th><?php _e('Title');
    ?></th><th><?php _e('Price', 'woocommerce');
    ?></th></th><th>%</th></tr>
 	<tr>
 		<td style="color:green;"><?php _e('All countries defaults','simple-multi-currency-for-woocommerce'); ?></td>
 		<td><input style="min-width:300px;" name="gateway[<?php print $key;
    ?>][default]" type="text" value="<?php if(isset($arr[$key]['default']['title'])) {
    print  $arr[$key]['default']['title'];
}
else {
    print $object->get_method_title();
}
?>"></td>
 		<td>
    <input name="gateway[<?php print $key;
    ?>][default][amount]" type="number" step="0.1" value="<?php $am = $arr[$key]['default']['amount'];  if(intval($am) or doubleval($am)) {
    print $am;
}
else {
    print "0";
}
?>"></td>
 		<td>
    <input name="gateway[<?php print $key;
    ?>][default][percent]" type="number" step="0.1" value="<?php $ac = $arr[$key]['default']['percent'];  if(intval($ac) or doubleval($ac)) {
    print $ac;
}
else {
    print "0";
}
?>"></td>
 	</tr>
<tr>
    <td><?php _e('Disable on Free Shipping', 'simple-multi-currency-for-woocommerce'); ?></td>
    <td><input name="freeshipping[<?php print $key; ?>][free]" vlaue="1" type="checkbox" <?php if(isset($freeshipping[$key]['free'])){ print "checked"; } ?> ></td>
    <td></td>
</tr>
<tr>
    <td><?php _e('Disable on Zero Shipping', 'simple-multi-currency-for-woocommerce'); ?></td>
    <td><input name="freeshipping[<?php print $key; ?>][zero]" vlaue="1" type="checkbox" <?php if(isset($freeshipping[$key]['zero'])){ print "checked"; } ?> ></td>
    <td></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<?php foreach($currencies as $k=>$c) {
    ?><tr>
    <td><?php  print smcfw_get_flag($k, $c).' '; print $c;
    ?></td>
    <td><input style="min-width: 300px;
    " name="gateway[<?php print $key;
    ?>][<?php print $k;
    ?>][title]" type="text" value="<?php if(isset($arr[$key][$k]['title'])) {
    print  $arr[$key][$k]['title'];
}
else {
    print $object->get_method_title();
}
?>"></td>
<td>
    <input name="gateway[<?php print $key;
    ?>][<?php print $k;
    ?>][amount]" type="number" step="0.1" value="<?php if(isset($arr[$key][$k]['amount'])){ $am = $arr[$key][$k]['amount'];  if(intval($am) or doubleval($am)) {
    print $am;
}
else {
    print "0";
}} else{ print "0"; }
?>"></td>
<td>
    <input name="gateway[<?php print $key;
    ?>][<?php print $k;
    ?>][percent]" type="number" step="0.1" value="<?php if(isset($arr[$key][$k]['percent'])){ $ap = $arr[$key][$k]['percent'];  if(intval($ap) or doubleval($ap)) {
    print $ap;
}
else {
    print "0";
}} else{ print "0"; }
?>"></td>
</tr>
<?php
}
?>
</table>
</div>
<?php
}
?>
<div id="tab-general" class="div-tabs">
    <h4><?php _e('Set currency symbols','simple-multi-currency-for-woocommerce'); ?>:</h4>
    <table>
<?php if($currencies){ foreach($currencies as $k=>$c) {
    ?>
        <tr><td><?php print smcfw_get_flag($k, $c).' '; print $c;
    ?>: </td><td><input type="text" name="currency[<?php print $k;
    ?>]" value="<?php if(isset($mena[$k])){ print $mena[$k]; }
    ?>"></td></tr>
<?php }
}
?>
    </table>
</div>
<input type="hidden" name="action" value="smcfwupdatefeesaction">
<input type="hidden" name="tab" value="smcfwtab">
<input type="hidden" name="security" value="<?php print wp_create_nonce( 'smcfwupdatefeesaction_nonce' ) ?>">
<div class="button-div-footer" style="margin-top:2em;">
<button class="button button-primary" id="submit-form_b" type="submit"><?php _e('Submit');
    ?></button>
</div>
</form>
<div style="padding:1em;"><hr>
<?php do_action('smcfw_action_fees_page_func_footer'); ?>
</div>
<?php
}


add_action('admin_footer','smcfw_fees_page_footer_js',100);
function smcfw_fees_page_footer_js(){ 
global $smcfw_plugin_admin_page;
$screen = get_current_screen();
if ( $screen->id != $smcfw_plugin_admin_page ){ return; }
	?>
    <script>
    jQuery(document).ready(function($) {
    $( "form#setform" ).on( "submit", function( event ) {
        $('#submit-form_b').html('<span style="vertical-align: text-top;" class="dashicons dashicons-backup"></span>');
        $('#submit-form_b').removeClass('button-primary');
        $('#submit-form_b').addClass('button-secondary');
        $('#submit-form_b').attr('disabled',true);
    
    event.preventDefault();
    var dataar = $( this ).serialize();
    $.ajax( {
    url: '<?php print SMCFW_AJAX_URL; ?>', type: 'POST', data: dataar,}
)
  .done(function() {
    //console.log("success");
    $('#submit-form_b').addClass('button-success');
}
)
  .fail(function() {
    //console.log("error");
    $('#submit-form_b').addClass('button-error');
}
)
  .always(function() {
    //console.log("complete");
    $('#submit-form_b').attr('disabled',false);
    $('#submit-form_b').html('<?php _e('Submit'); ?>');
}
);
    return false;
}
);
    $('body').on('click', 'a.smcfw-nav-tab', function(event) {
    var t = $(this);
    event.preventDefault();
    $('a.smcfw-nav-tab').each(function() {
    $(this).removeClass('nav-tab-active');
}
);
    t.addClass('nav-tab-active');
    $('.div-tabs').each(function() {
    $(this).addClass('tab-hide');
}
);
    $(t.attr('href')).removeClass('tab-hide');
    /* Act on the event */

}
);
}
);
    </script>
<?php
}