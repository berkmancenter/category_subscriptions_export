<?php
/*
Plugin Name: Categories Subscription Export
Plugin URI: https://github.com/berkmancenter/category_subscriptions_export
Author: Tomas Reimers
Author URI: http://tomasreimers.com
Description: A plugin to export data from the  category subscriptions plugin.
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

?>
