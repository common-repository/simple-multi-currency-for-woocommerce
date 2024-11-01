<?php
function custom_add_menu_meta_box( $object ) {
	add_meta_box( 'simple-multi-currency-menu-metabox', 'Simple multi-currency for Woocommerce by Invelity', 'smcfw_switcher_menu_meta_box', 'nav-menus', 'side', 'default' );
	return $object;
}
add_filter( 'nav_menu_meta_box_object', 'custom_add_menu_meta_box', 10, 1);

function smcfw_switcher_menu_meta_box(){
	?>
	<style>
		.simple-multi-currency-menu-metabox h3{ background:transparent !important; padding: 5px 10px 5px 60px !important; }
		.simple-multi-currency-menu-metabox { background-color:#eafeea;  background-size: 55px 55px; background-repeat:no-repeat; background-image: url(<?php echo SMCFW_ASSETS_URL; ?>/images/icon-256x256.png); }
	</style>
	<?php
	global $nav_menu_selected_id;
	$walker = new Walker_Nav_Menu_Checklist();

	$current_tab = 'all';
if ( isset( $_REQUEST['authorarchive-tab'] ) && 'admins' == $_REQUEST['authorarchive-tab'] ) {
	$current_tab = 'admins';
}elseif ( isset( $_REQUEST['authorarchive-tab'] ) && 'all' == $_REQUEST['authorarchive-tab'] ) {
	$current_tab = 'all';
}

$authors = get_users( array( 'orderby' => 'nicename', 'order' => 'ASC', 'who' => 'authors' ) );
$countries = smcfw_get_allowed_countries();
$author = array();
$c = array();
$absolute_url = smcfw_full_url( $_SERVER );
foreach($countries as $key=>$value){ 
 	$author['classes'] = array($key.'-smcfw-flag-switch-menu-item','smcfw-flags-switch-menu-item');
	$author['type'] = 'custom';
	$author['object_id'] = $key;
	$author['title'] = $value;
	$author['object'] = 'custom';
	$author['url'] = '#'.$key;
	$author['attr_title'] = $key;
	$c[]=(object)$author;
}
$admins = array();
//var_dump($authors);
/* set values to required item properties */
/*
print '<pre>';	var_dump((object)$author); print '</pre>';
foreach ( $authors as $author ) {
	$author->classes = array();
	$author->type = 'custom';
	$author->object_id = $author->nickname;
	$author->title = $author->nickname . ' - ' . implode(', ', $author->roles);
	$author->object = 'custom';
	$author->url = get_author_posts_url( $author->ID ); 
	$author->attr_title = $author->displayname;
	if( $author->has_cap( 'edit_users' ) ){
		$admins[] = $author;
	}
}
print '<pre style="background:silver;">';	var_dump($authors); print '</pre>';
*/
$removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
?>

<div id="authorarchive" class="categorydiv">
	<ul style="display:none;" id="authorarchive-tabs" class="authorarchive-tabs add-menu-item-tabs">
		<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
			<a class="nav-tab-link" data-type="tabs-panel-authorarchive-all" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'authorarchive-tab', 'all', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-authorarchive-all">
				<?php _e( 'View All' ); ?>
			</a>
		</li><!-- /.tabs -->

		<li <?php echo ( 'admins' == $current_tab ? ' class="tabs"' : '' ); ?>>
			<a class="nav-tab-link" data-type="tabs-panel-authorarchive-admins" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'authorarchive-tab', 'admins', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-authorarchive-admins">
				<?php _e( 'Admins' ); ?>
			</a>
		</li><!-- /.tabs -->
	</ul>

	<div id="tabs-panel-authorarchive-all" class="tabs-panel tabs-panel-view-all <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
	<ul id="authorarchive-checklist-all" class="categorychecklist form-no-clear">
	<?php
		//echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $authors), 0, (object) array( 'walker' => $walker) );
		echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $c), 0, (object) array( 'walker' => $walker) );
	?>
	</ul>
</div><!-- /.tabs-panel -->

<div style="display:none;" id="tabs-panel-authorarchive-admins" class="tabs-panel tabs-panel-view-admins <?php echo ( 'admins' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
	<ul id="authorarchive-checklist-admins" class="categorychecklist form-no-clear">
	<?php
	//echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $admins), 0, (object) array( 'walker' => $walker) );
	?>
	</ul>
</div><!-- /.tabs-panel -->

<p class="button-controls wp-clearfix">
	<span class="list-controls">
		<a href="<?php echo esc_url( add_query_arg( array( 'authorarchive-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#authorarchive" class="select-all"><?php _e('Select All'); ?></a>
	</span>
	<span class="add-to-menu">
		<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-authorarchive-menu-item" id="submit-authorarchive" />
		<span class="spinner"></span>
	</span>
</p>

</div><!-- /.categorydiv -->
<?php
}

add_action('get_footer', function(){
?>
<script>
jQuery(document).ready(function($) {
var lohref = window.location.href.toString();


$('.smcfw-flags-switch-menu-item').each(function() {
lohref = lohref.replace('&smcfw_change_currency=' + $(this).children('a').attr('title'),'');
lohref = lohref.replace('?smcfw_change_currency=' + $(this).children('a').attr('title'),'');
});
if(lohref.includes("?")){ var dot = '&'; } else{ var dot = '?';}
$('.smcfw-flags-switch-menu-item').each(function() {
	$(this).children('a').attr('href',lohref + dot + 'smcfw_change_currency=' + $(this).children('a').attr('title'));
});	


});
</script>
<?php	
});