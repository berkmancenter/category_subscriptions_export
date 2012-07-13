<?php

class Category_subscription_export_db_manager{
	public function get_individual_header(){
		// get globals 
		global $wpdb;
		// gather data
		$prepared = $wpdb->prepare("SELECT DISTINCT t.name AS name, t.term_id AS id FROM wp_terms t INNER JOIN wp_term_taxonomy n ON t.term_id = n.term_id WHERE n.taxonomy = 'category'");
		$results = $wpdb->get_results($prepared, OBJECT);
		foreach ($results as $result){
			$toReturn[] = $result->id;
		}
		return $toReturn;
	}
	public function get_individual_data($category_ids){
		// get globals 
		global $wpdb;
		// gather data
		$prepared = $wpdb->prepare("SELECT u.display_name as user_name, u.ID as user_id, u.user_email as user_email, m.meta_value as user_class_year FROM wp_users u LEFT JOIN wp_usermeta m ON m.user_id = u.ID AND m.meta_key='class_year'");
		$people = $wpdb->get_results($prepared, OBJECT);
		$toReturn = array();
		foreach ($people as $person){
			$currentIndex = count($toReturn);
			$toReturn[$currentIndex] = array();
			$toReturn[$currentIndex][] = $person->user_name;
			$toReturn[$currentIndex][] = $person->user_email;
			$toReturn[$currentIndex][] = $person->user_class_year;
			foreach ($category_ids as $category_id){
				$toReturn[$currentIndex][] = $this->check_if_subscribed($person->user_id, $category_id);
			}
		}
		return $toReturn;
	}
	public function check_if_subscribed($user, $category){
		// get globals 
		global $wpdb;
		// gather data
		$prepared = $wpdb->prepare("SELECT COUNT(id) as subscribed FROM wp_cat_sub_categories_users c WHERE category_ID = %d AND user_ID = %d", array($category, $user));
		$results = $wpdb->get_results($prepared, OBJECT);
		return $results[0]->subscribed;
	}
	public function get_aggregate_data(){
		// get globals
		global $wpdb;
		// gather data
		$prepared = $wpdb->prepare("SELECT DISTINCT t.name, COUNT(c.category_ID) AS subscribed, t.term_id AS id FROM wp_terms t INNER JOIN wp_term_taxonomy n ON t.term_id = n.term_id LEFT JOIN wp_cat_sub_categories_users c ON t.term_id = c.category_ID WHERE n.taxonomy = 'category' GROUP BY t.name");
		$results = $wpdb->get_results($prepared, OBJECT);
		return $results;
	}
	public function update_class_year_bulk_edit(){
		// piggy back on category subscriptions passing User ID's
		$user_ids = isset($_GET['csi']) ? $_GET['csi'] : array();
		foreach($user_ids as $user_ID){
			if (isset($_GET['CSECY' . $user_ID])){
				update_user_meta($user_ID, 'class_year', $_GET['CSECY' . $user_ID], get_user_meta($user_id, 'class_year', TRUE));
			}
		}
	}
	public function update_class_year_profile($user_ID){
		if (isset($_POST['CSECY' . $user_ID])){
			update_user_meta($user_ID, 'class_year', $_POST['CSECY' . $user_ID], get_user_meta($user_ID, 'class_year', TRUE));
		}
	}
}

?>