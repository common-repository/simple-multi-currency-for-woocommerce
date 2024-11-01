<?php 
class SimpleMultiCurrencyForWoocommerceProduct extends SimpleMultiCurrencyForWoocommerce
{
 
    public function __construct()
    {
        $this->initialize();
    }

    public function initialize()
    {
    add_action('smcfw_action_add_price_fields_to_product_end',array($this,'js_scripts_init'));
    add_action('smcfw_action_js_datepicker_inputs',array($this,'js_scripts_datepicker'));
    }

  
public function js_datepicker_inputs($settings){
    $from = $settings['from'];
    $to = $settings['to'];
    $id = $settings['id'];
    $format = $settings['format']; //yy-mm-dd
    return '<script>
  jQuery( function() {
    var dateFormat = "'.$format.'",
      from'.$id.' = jQuery( "'.$from.'" )
        .datepicker({
          defaultDate: "+0d",
          dateFormat: "'.$format.'",
          changeMonth: true,
          numberOfMonths: 1
        })
        .on( "change", function() {
          to'.$id.'.datepicker( "option", "minDate", getDate'.$id.'( this ) );
        }),
      to'.$id.' = jQuery( "'.$to.'" ).datepicker({
        defaultDate: "+1d",
        dateFormat: "'.$format.'",
        changeMonth: true,
        numberOfMonths: 1
      })
      .on( "change", function() {
        from'.$id.'.datepicker( "option", "maxDate", getDate'.$id.'( this ) );
      });
 
    function getDate'.$id.'( element ) {
      var date;
      try {
        date = jQuery.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
       return date;
    }
  } );
  </script>';
}

public function js_schedule_fields(){
    return '<script>
  jQuery( function() {
  jQuery("body").on("click",".smcfw_cancel_sale_schedule",function(event){ event.preventDefault(); 
	if(jQuery(this).hasClass("smcfw-simple-product")){
		jQuery(this).parent(".smcfw-sale-price-dates-fields").each(function(){ jQuery(this).children("input").val(""); });
		jQuery(this).parent(".smcfw-sale-price-dates-fields").fadeOut(1); 
		jQuery(this).parent().prev().find(".smcfw_sale_schedule").fadeIn(1);
  	}

if(jQuery(this).hasClass("smcfw-variation-product")){
jQuery(jQuery(this).attr("href")).each(function(){ jQuery(this).children("p").find("input").val(""); });
jQuery(jQuery(this).attr("href")).fadeOut(1);
 jQuery(this).parent().find(".smcfw_sale_schedule").fadeIn(1);
 jQuery(this).fadeOut(1);
 	}
    return false; });

jQuery("body").on("click",".smcfw_sale_schedule",function(event){ event.preventDefault(); jQuery(jQuery(this).attr("href")).fadeIn(1); 
  jQuery(this).parent("label").children(".smcfw_cancel_sale_schedule").fadeIn(1);  jQuery(this).fadeOut(1); return false; });
  } );
  </script>';
}

public function js_check_inputs_container_fields(){
    return '
    <script>
    jQuery(function(){
        jQuery("body").on("keyup",".smcfw_sale_price_input",function(event){ event.preventDefault(); 
var thisfield = jQuery(this);
var sale_price = parseFloat( window.accounting.unformat(thisfield.val(), woocommerce_admin.mon_decimal_point ) );
var regular_price = parseFloat( window.accounting.unformat(thisfield.closest(".smcfw-inputs-container").find(".smcfw_regular_price_input").val(), woocommerce_admin.mon_decimal_point ) );
if ( sale_price >= regular_price ) {
                     var offset = thisfield.position();
                         if ( thisfield.parent().find( ".wc_error_tip" ).length === 0 ) {
                                thisfield.after( "<div class=\"wc_error_tip iwc_error\">" + "'. __('Sale price must be higher value then regualr price.','').'" + "</div>" );
                                thisfield.parent().find( ".wc_error_tip" )
                                        .css( "left", offset.left + thisfield.width() - ( thisfield.width() / 2 ) - ( jQuery( ".wc_error_tip" ).width() / 2 ) )
                                        .css( "top", offset.top + thisfield.height() )
                                        .fadeIn( "100" );
                        }
         thisfield.val("");
} else { }
});
    });

</script>
';

}


function js_scripts_init(){
  print apply_filters('smcfw_filter_js_schedule_fields', $this->js_schedule_fields());
  print apply_filters('smcfw_filter_js_check_inputs_container_fields',$this->js_check_inputs_container_fields());
  print apply_filters('smcfw_filter_add_price_fields_to_product_footer','');
}

function js_scripts_datepicker($settings){
    print apply_filters('smcfw_filter_js_datepicker_inputs', $this->js_datepicker_inputs($settings), $settings);
}

}