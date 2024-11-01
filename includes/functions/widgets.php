<?php
// Register and load the widget
function smcfw_load_widget() {
    register_widget( 'smcfw_widget' );
}
add_action( 'widgets_init', 'smcfw_load_widget' );
 

class smcfw_widget extends WP_Widget {
	// class constructor
	public function __construct() {
	$widget_ops = array( 
		'classname' => 'smfcfw_widget',
		'description' => __( 'Widget to embed the switcher in sidebar.', 'simple-multi-currency-for-woocommerce' ),
	);
	parent::__construct( 'smcfw_widget', __('Simple multi-currency for Woocommerce Widget', 'simple-multi-currency-for-woocommerce'), $widget_ops );
}
	
	// output the widget content on the front-end
	public function widget( $args, $instance ) {
    if(apply_filters('smcfw_filter_init_add_switcher_callback_css_widget_onlyshop',false)==true){ return; }
	echo $args['before_widget'];
	if ( ! empty( $instance['title'] ) ) {
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
	}

print apply_filters('smcfw_filter_init_add_switcher_callback_css_widget','
<style>
	#widget-language-selector-div{ }
	#widget-language-selector-div a{ display:block;  }
	#widget-language-selector-div a.grayscale{ filter:grayscale(100); }
	#widget-language-selector-div a.nograyscale{ filter:grayscale(0); }
	#widget-language-selector-div a:hover{
	-moz-transition: all .5s ease-in;
    -ms-transition: all .5s ease-in;
    -o-transition: all .5s ease-in;
    transition: all .5s ease-in;
    filter:grayscale(0);
	}
</style>');
print '<div id="widget-language-selector-div" class="shadow">';
do_action('smcfw_display_switcher');
print '</div>';
	echo $args['after_widget'];
}

	// output the option form field in admin Widgets screen
	public function form( $instance ) {}

	// save options
	public function update( $new_instance, $old_instance ) {}
}