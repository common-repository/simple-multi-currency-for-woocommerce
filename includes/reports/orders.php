<?php
  global $wpdb;
  $to = get_woocommerce_currency();
  $colors = array();
   $countries = smcfw_get_allowed_countries();
    global $smcwf_settings;
    $settings = $smcwf_settings;
  $numcountries = array(); $i=0;
  foreach($countries as $key=>$value){ $numcountries[$key]=$i; $i++; }

    $the_queries = $wpdb->get_results(apply_filters('smcfw_filter_report_orders_get_results_sql',"SELECT * FROM $wpdb->posts WHERE `post_type` LIKE 'shop_order' AND `post_status` NOT LIKE 'trash' ORDER BY `ID` DESC",$class));
   // print '<pre>'; print_r($the_queries); print '</pre>';
    foreach($the_queries as $the_query) {
    	$id = get_post_meta( $the_query->ID, apply_filters('smcfw_filter_get_country_sales_country','_billing_country'), true );
    	if(!isset($arr[$id]['count'])){ $arr[$id]['count']=0; }
    	if(!isset($arr[$id]['total'])){ $arr[$id]['total']=0; }
      $order = new WC_Order($the_query->ID);
      $order_total = get_post_meta( $the_query->ID, '_order_total', true );
      if(!isset($arr[$id][date('n',strtotime($order->get_date_created()))]['total'])){ $arr[$id][date('n',strtotime($order->get_date_created()))]['total']=0; }
      if(!isset($arr[$id][date('n',strtotime($order->get_date_created()))]['count'])){ $arr[$id][date('n',strtotime($order->get_date_created()))]['count']=0; }

      $arr[$id][date('n',strtotime($order->get_date_created()))]['total'] = $arr[$id][date('n',strtotime($order->get_date_created()))]['total'] + $order_total;
       $arr[$id][date('n',strtotime($order->get_date_created()))]['count']++;
       $arr[$id][date('n',strtotime($order->get_date_created()))]['country_code']=$key;

    	$arr[$id]['total'] = $arr[$id]['total'] + $order_total;
    	$arr[$id]['count']++;
    	$ccc = '#000000';
       $arr[$id]['color']=apply_filters('smcfw_filter_report_orders_line_color', $ccc, $numcountries[$id]);
    }
?>
<style>
  canvas{
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    background:#fff;
    border: 1px solid #e5e5e5;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
  }
  .tab-hide{ display:none; }
  </style>
<!-- nav - start -->
<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
<a href="#tab-sales" class="smcfw-nav-tab nav-tab nav-tab-active"><?php _e('Sales','woocommerce'); ?></a>
<a href="#tab-counts" class="smcfw-nav-tab nav-tab"><?php _e('Number of orders','woocommerce'); ?></a>
</nav>
<!-- nav - end -->
<!-- tab sales - start -->
<div class="div-tabs" id="tab-sales">
    <div style="width:100%;">
    <canvas id="canvas-totals"></canvas>
  </div>
<script>
 //   var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var config = {
      type: 'line',
      data: {
        labels: ['<?php _e('January'); ?>', '<?php _e('February'); ?>', '<?php _e('March'); ?>', '<?php _e('April'); ?>', '<?php _e('May'); ?>', '<?php _e('June'); ?>', '<?php _e('July'); ?>', '<?php _e('August'); ?>', '<?php _e('September'); ?>', '<?php _e('October'); ?>', '<?php _e('November'); ?>', '<?php _e('December'); ?>'],
        datasets: [
        <?php foreach($arr as $key=>$value){ $color=$value['color']; ?>
        {
          label: '<?php print $countries[$key]; ?>',
          backgroundColor: '<?php print $color; ?>',
          borderColor: '<?php print $color; ?>', //window.chartColors.red,
          data: [
       <?php for ($i = 1; $i <= 12; $i++) {
        if(!isset($value[$i]['total'])){ print '0,'; } else{
          $sfrom = smcfw_get_currency_code($key);
          if($sfrom==$to){ print round($value[$i]['total'],2).','; } else{
         print round(smcfw_convert($value[$i]['total'],$sfrom,$to),2).',';
       }
  }
} ?>
          ],
          fill: false,
        }, 
        <?php } ?>
        ]
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: ''
        },
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: '<?php _e('nearest'); ?>',
          intersect: true
        },
        scales: {
          xAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: '<?php _e('Month'); ?>'
            }
          }],
          yAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: '<?php _e('Value'); if(isset($settings['recalculate_currency_rates'])){ print " (".get_woocommerce_currency().")"; } ?>'
            }
          }]
        }
      }
    };

  //  window.onload = function() {
      var ctx = document.getElementById('canvas-totals').getContext('2d');
      //window.myLine = new Chart(ctx, config);
      var myLine = new Chart(ctx, config);
   // };
</script>
</div>
<!-- tab sales - end -->
<!-- tab counts - start -->
<div class="div-tabs tab-hide" id="tab-counts">

   <div style="width:100%;">
    <canvas id="canvas-counts"></canvas>
  </div>
<script>
    var config_counts = {
      type: 'line',
      data: {
        labels: ['<?php _e('January'); ?>', '<?php _e('February'); ?>', '<?php _e('March'); ?>', '<?php _e('April'); ?>', '<?php _e('May'); ?>', '<?php _e('June'); ?>', '<?php _e('July'); ?>', '<?php _e('August'); ?>', '<?php _e('September'); ?>', '<?php _e('October'); ?>', '<?php _e('November'); ?>', '<?php _e('December'); ?>'],
        datasets: [
        <?php foreach($arr as $key=>$value){ $color=$value['color']; ?>
        {
          label: '<?php print $countries[$key]; ?>',
          backgroundColor: '<?php print $color; ?>',
          borderColor: '<?php print $color; ?>', //window.chartColors.red,
          data: [
       <?php for ($i = 1; $i <= 12; $i++) {
        if(!isset($value[$i]['count'])){ print '0,'; } else{
    print $value[$i]['count'].',';

  }
} ?>
          ],
          fill: false,
        }, 
        <?php } ?>
        ]
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: ''
        },
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: '<?php _e('nearest'); ?>',
          intersect: true
        },
        scales: {
          xAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: '<?php _e('Month'); ?>'
            }
          }],
          yAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: '<?php _e('Value'); ?>'
            }
          }]
        }
      }
    };

   
      var ctx_counts = document.getElementById('canvas-counts').getContext('2d');
      var myLine_counts = new Chart(ctx_counts, config_counts);
   
</script>

</div>
<!-- tab counts - end -->
<br><br>
    <table class="widefat">
      <thead>
          <tr>
              <th><strong><?php _e('Country','woocommerce'); ?></strong></th>
              <th><strong><?php _e('Number of orders','woocommerce'); ?></strong></th>
              <th><strong><?php _e('Sales','woocommerce'); ?></strong></th>
          </tr>
      </thead>
      <tbody>
<?php
if(!empty($arr)){
foreach($arr as $key=>$value){
?> 
          <tr>
              <td><span style="width:16px; margin-right:5px; box-shadow: 0 1px 1px rgba(0,0,0,.04); height:16px; display:inline-block; background:<?php print $value['color']; ?>"></span><?php print smcfw_get_flag($key, $countries[$key]).' '; print $countries[$key]; ?></td>
      <td><?php print $value['count']; ?></td>
              <td><?php
            $from = smcfw_get_currency_code($key);
            if($to==$from){ echo wc_price($value['total']); } else{
              echo smcfw_get_price(smcfw_get_country_currency($key), $value['total']);
              if(isset($settings['recalculate_currency_rates'])){
              print ' ('. wc_price( smcfw_convert($value['total'],$from,$to)).')'; 
          }
              ?></td>
            
          </tr>
          <?php }
      }
    }
	?>
      </tbody>
    </table>
<script>
	jQuery(document).ready(function($) {
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
	});

	  function smcfw_random_color(){
          var colorR = Math.floor((Math.random() * 256));
      var colorG = Math.floor((Math.random() * 256));
      var colorB = Math.floor((Math.random() * 256));
      return "rgb(" + colorR + "," + colorG + "," + colorB + ")";
  }
</script>