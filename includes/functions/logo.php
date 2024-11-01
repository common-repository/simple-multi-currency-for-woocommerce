<?php
function smcfw_logo($echo = false){
	$logo = '<div class="smcfwLogo" style="text-align:right; margin-bottom:5px;">
<a style="margin-left:5px; text-decoration:none;" title="" target="_blank" href="'.esc_url('https://www.invelity.com/').'">
<svg style="width:60px;" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 162 23" xml:space="preserve"><path d="M0,23h6.7V0H0V23z"></path>
    <path d="M9.9,23h6.7V10.9L25.1,23h5.7V0h-6.7v12.2L15.5,0H9.9V23z"></path>
    <path d="M42.1,23h7.1l8.8-23h-7l-5.3,15.2L40.2,0h-7L42.1,23z"></path>
    <path d="M60.4,23h17.3v-5.1H67v-3.8h9.3V8.9H67V5.1h10.6V0H60.4V23z"></path>
    <path d="M79.8,23h17v-5.1H86.5V0h-6.7V23z"></path>
    <path d="M125.1,23h6.7V5h6.4V0h-19.5v5h6.4V23z"></path>
    <path d="M147.1,23h6.7v-8.4L162,0h-7l-4.6,8.7L145.8,0h-7l8.2,14.6V23z"></path>
    <path fill="#e23b73" class="highlight" d="M97.7,23h6.8l13.2-22.9h-6.8L97.7,23z"></path>
</svg></a>
</div>';
$logo = apply_filters('smcfw_filter_logo',$logo);
if(apply_filters('smcfw_filter_logo_echo',$echo)==true){ echo $logo; }
return $logo;
}

add_action('smcfw_action_settings_page_func_end', 'smcfw_logo');
add_filter('smcfw_filter_logo_echo',function(){ return true; });

add_action('smcfw_action_fees_page_func_footer', 'smcfw_logo');