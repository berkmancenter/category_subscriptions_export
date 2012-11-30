<?php
/*
Plugin Name: Categories Subscription Export
Plugin URI: http://github.com/berkmancenter/category_subscriptions_export
Description: A custom export for category subscriptions.
Version: 0.1
*/

// Need get_userdata()
require_once(ABSPATH . 'wp-includes/pluggable.php');

// get necessary classes
require_once("includes/category_subscription_export_html_manager.php");
$cat_sub_export_html = new Category_subscription_export_html_manager();
require_once("includes/category_subscription_export_db_manager.php");
$cat_sub_export_db = new Category_subscription_export_db_manager();

// hook into menu - admin page
add_action('admin_menu', 'add_category_subscription_menu_hook');
function add_category_subscription_menu_hook(){
	global $cat_sub_export_html;
	add_options_page(
		__('Export Category Subscriptions Data'),
		__('Export Category Subscriptions Data'), 
		'manage_options', 
		'categories-subscription-export', 
		array($cat_sub_export_html, 'export_admin_page')
	);
}

// create new page
add_action('init', array($cat_sub_export_html, 'export_CSV'));

// add class hooks for bulk editing
if (current_user_can('remove_users')){
	add_filter('manage_users_columns', array($cat_sub_export_html, 'create_inline_column'));
	add_filter('manage_users_custom_column', array($cat_sub_export_html, 'create_inline_class'), 10, 3);
	add_action('admin_head', array($cat_sub_export_db, 'update_class_year_bulk_edit'));
}

// edit user profile page
if(current_user_can('remove_users')){
	add_action( 'edit_user_profile', array( $cat_sub_export_html, 'create_independant_class' ) );
	add_action( 'edit_user_profile_update', array( $cat_sub_export_db, 'update_class_year_profile' ) );
}

// update user edits
add_action( 'profile_personal_options', array( $cat_sub_export_html, 'create_independant_class' ) );
add_action( 'personal_options_update', array( $cat_sub_export_db, 'update_class_year_profile' ) );

?>