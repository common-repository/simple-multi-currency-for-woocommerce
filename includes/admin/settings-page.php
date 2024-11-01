<?php
add_action( 'admin_menu', 'smcfw_settings_plugin_menu' );

function smcfw_settings_plugin_menu() {
    global $smcfw_plugin_settings_page;
    $smcfw_plugin_settings_page = add_submenu_page( 
        'options-general.php', 
        '', 
        '<span style="margin-right:5px; color:#4DB435;" class="dashicons dashicons-admin-settings"></span><b style="color:#4DB435;">'.__('Settings').'</b><div style="font-size:xx-small;">Simple multi-currency for Woocommerce by <svg style="width:50px; fill:#fff;" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 162 23" xml:space="preserve"><path d="M0,23h6.7V0H0V23z"></path>
    <path d="M9.9,23h6.7V10.9L25.1,23h5.7V0h-6.7v12.2L15.5,0H9.9V23z"></path>
    <path d="M42.1,23h7.1l8.8-23h-7l-5.3,15.2L40.2,0h-7L42.1,23z"></path>
    <path d="M60.4,23h17.3v-5.1H67v-3.8h9.3V8.9H67V5.1h10.6V0H60.4V23z"></path>
    <path d="M79.8,23h17v-5.1H86.5V0h-6.7V23z"></path>
    <path d="M125.1,23h6.7V5h6.4V0h-19.5v5h6.4V23z"></path>
    <path d="M147.1,23h6.7v-8.4L162,0h-7l-4.6,8.7L145.8,0h-7l8.2,14.6V23z"></path>
    <path fill="#e23b73" class="highlight" d="M97.7,23h6.8l13.2-22.9h-6.8L97.7,23z"></path>
</svg></div>', 
        'manage_options', 
        'smcfw-general-settings-page', 
        'smcfw_settings_page_func' 
    );
}

function smcfw_settings_page_func(){
    global $woocommerce;
    global $smcwf_settings;
    $settings = $smcwf_settings;

    do_action('smcfw_action_settings_page_func_start');
    ?>
    <style>
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

body{ background:#fff !important; }
.smcfw-settings-wrapper{ max-width:772px; margin:auto; font-size:medium; box-shadow: 0 8px 16px 0 rgba(85,93,102,.3); margin-top:1em; }
.smcfw-settings-wrapper .content{  padding:10px; }
.smcfwLogo{ max-width:772px; margin:auto; }

</style>
    <div class="smcfw-settings-wrapper">
    <div style="margin-bottom:1em; height:250px; background-repeat:no-repeat; background-image: url(<?php echo SMCFW_ASSETS_URL; ?>/images/banner-772x250.jpg);"><img style="height:100px; width:auto; margin:10px;" src="<?php echo SMCFW_ASSETS_URL; ?>/images/icon-256x256.png" alt=""><h3><div style="position:relative; bottom:-4.5em; text-align:center;"><?php _e('Settings'); ?> Simple multi-currency for Woocommerce by Invelity</div></h3></div><hr>
    <div sytle="margin-top:1em; margin-bottom:2em; display:block;" class="content">
<form style="width:90%; margin:auto;" id="setform" action="" method="post">
<table style="width:100%;">
	<?php do_action('smcfw_action_settings_page_form_content_start'); ?>
        <tr>
<td style="width:70%;"><?php _e('Display languages switcher','simple-multi-currency-for-woocommerce'); ?></td>
<td style="width:30%;">
<label class="switch">
  <input name="settings[display_switcher]" vaule="1" type="checkbox" <?php if(isset($settings['display_switcher'])){ print "checked"; } ?> >
  <span class="slider round"></span>
</label>
</td>
        </tr>
<!-- Since version 1.0.11 - start -->
<tr>
<td><?php _e('Display languages switcher in Cart','simple-multi-currency-for-woocommerce'); ?></td>
<td>
  <label class="switch">
    <input name="settings[display_switcher_in_cart]" vaule="1" type="checkbox" <?php if(isset($settings['display_switcher_in_cart'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td>
</tr>
<tr>
<td><?php _e('Display languages switcher in Checkout','simple-multi-currency-for-woocommerce'); ?></td>
<td>
  <label class="switch">
    <input name="settings[display_switcher_in_checkout]" vaule="1" type="checkbox" <?php if(isset($settings['display_switcher_in_checkout'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td>
</tr>
<tr>
<td><?php _e('Display languages switcher in pages with Woocommerce use','simple-multi-currency-for-woocommerce'); ?></td>
<td>
  <label class="switch">
    <input name="settings[display_switcher_in_woocommerce]" vaule="1" type="checkbox" <?php if(isset($settings['display_switcher_in_woocommerce'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td>
</tr>
<tr>
<td><?php _e('Display languages switcher in pages with product shortcodes','simple-multi-currency-for-woocommerce'); ?></td>
<td>
  <label class="switch">
    <input name="settings[display_switcher_with_product_shorcode]" vaule="1" type="checkbox" <?php if(isset($settings['display_switcher_with_product_shorcode'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td>
</tr>

<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr>
<td><?php _e('Change currency based on shipping address','simple-multi-currency-for-woocommerce'); ?></td>
<td>
  <label class="switch">
    <input name="settings[use_shipping_address]" vaule="1" type="checkbox" <?php if(isset($settings['use_shipping_address'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td>
        </tr><!-- Since version 1.0.11 - end -->
<tr>
<td><?php _e('Rewrite default WooCommerce reports','simple-multi-currency-for-woocommerce'); ?></td>
<td>
  <label class="switch">
    <input name="settings[rewrite_default_reports]" vaule="1" type="checkbox" <?php if(isset($settings['rewrite_default_reports'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td>
        </tr>
<td><?php _e('Recalculate currency rates in reports','simple-multi-currency-for-woocommerce'); ?></td>
<td>
	<label class="switch">
    <input name="settings[recalculate_currency_rates]" vaule="1" type="checkbox" <?php if(isset($settings['recalculate_currency_rates'])){ print "checked"; } ?> >
      <span class="slider round"></span>
</label>
</td></tr>
<tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr>
        <?php if( function_exists('icl_object_id') ){ ?>
      <tr>
<td><?php _e('Enable support for WPML plugin','simple-multi-currency-for-woocommerce'); ?></td>
<td>
    <input name="settings[wpml_support]" vaule="1" type="checkbox" <?php if(isset($settings['wpml_support'])){ print "checked"; } ?> >
</td>
        </tr>
    <?php } ?>
    <?php do_action('smcfw_action_settings_page_form_content_end'); ?>
    </table>
<input type="hidden" name="action" value="smcfwupdatesettingaction">
<input type="hidden" name="tab" value="smcfwtab">
<input type="hidden" name="security" value="<?php print wp_create_nonce( 'smcfwupdatesettingaction_nonce' ) ?>">
<div class="button-div-footer" style="margin-top:2em;">
<button class="button button-primary" id="submit-form_b" type="submit"><?php _e('Save'); ?></button>
</div>
</form>
    </div>
    <br><br>
</div>
    <?php do_action('smcfw_action_settings_page_func_end'); 
}

add_action('smcfw_action_settings_page_func_start', function(){ $css = '<style>
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
.div-tabs, .button-div-footer, .smcfw-settings-wrapper {
  /*  max-width: 800px;
    margin: auto; */
}
.button-div-footer {
    text-align: left;
}

</style>'; 
print apply_filters('smcfw_action_settings_page_func_start_css',$css);
});

add_action('admin_footer','smcfw_settings_page_footer_js',100);
function smcfw_settings_page_footer_js(){ 
global $smcfw_plugin_settings_page;
$screen = get_current_screen();
if ( $screen->id != $smcfw_plugin_settings_page ){ return; }
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
    $('#submit-form_b').html('<?php _e('Settings was saved :)','simple-multi-currency-for-woocommerce'); ?>');
    setTimeout(function(){ $('#submit-form_b').attr('disabled',false); $('#submit-form_b').html('<?php _e('Save'); ?>'); }, 2000);

}
);
    return false;
}
);
});
</script>
<?php } ?>