<?php

class Category_subscription_export_html_manager{

	public function export_admin_page(){

		global $cat_sub_export_db;

		$tableData = $cat_sub_export_db->get_aggregate_data();

		// START HTML DOCUMENT
		?>

		<h2><?php _e('Export Category Subscriptions Data'); ?></h2>

		<table style="text-align:left">
			<tr>
				<th>Category</th>
				<th>Subscribers</th>
			</tr>
			<?php
				foreach ($tableData as $tableRow){
					$nameStr = get_category_parents($tableRow->id, FALSE, ' &raquo; ');
					if (substr($nameStr, -9) == ' &raquo; '){
						$nameStr = substr($nameStr, 0, strlen($nameStr) - 9);
					}
					echo('<tr><td>' . $nameStr . '</td><td>' . $tableRow->subscribed . '</td></tr>');
				}
			?>
		</table>

		<p class="submit"><a href="options-general.php?page=categories-subscription-export-csv" class="button-primary"><?php _e('Export Individual Data'); ?></a></p> 
		<?php
		// END HTML DOCUMENT

	}

	public function export_CSV(){
		// make sure correct page
		if ($_GET['page'] === "categories-subscription-export-csv" && strstr($_SERVER['REQUEST_URI'], "options-general.php") !== FALSE){
			// make sure correct permissions
			if (current_user_can('remove_users')){
				// grab globals
				global $cat_sub_export_db;
				// open stream
				$output = fopen("php://output", "w");
				// toss headers
				header("Content-type: application/csv");
				header("Content-Disposition: attachment; filename=categories-subscription-data.csv");
				header("Pragma: no-cache");
				header("Expires: 0");
				// write it
				$head = $cat_sub_export_db->get_individual_header();
				// show heirarchy
				$headers = array();
				foreach ($head as $category){
					$nameStr = get_category_parents($category, FALSE, ' > ');
					if (substr($nameStr, -3) == ' > '){
						$nameStr = substr($nameStr, 0, strlen($nameStr) - 3);
					}
					$headers[] = $nameStr;
					$headers[] = $nameStr . " Preferences";
				}
				// add some headers
				array_unshift($headers, "Name", "Email", "Class Year");
				fputcsv($output, $headers);
				$data = $cat_sub_export_db->get_individual_data($head);
				foreach ($data as $datum){
					fputcsv($output, $datum);
				}
				// close stream
				fclose($output);
				// prevent further output
				exit();
			}
			else {
				exit("Insufficient Permissions");
			}
		}
	}

	public function create_inline_column($defaults){
		$defaults['class_year'] = __('Class Year');
		return $defaults;
	}

	public function create_inline_class($empty = '', $column_name, $user_id){
		if ($column_name == 'class_year'){
			$output = get_user_meta($user_id, 'class_year', TRUE);
			return ('<input type="text" style="width:75px" name="CSECY' . $user_id . '" value="' . $output . '" />');
		} 
		return $empty;
	}

	public function create_independant_class($user){
		$output = get_user_meta($user->ID, 'class_year', TRUE);
		?>
			<h3><?php _e('Class year'); ?></h3>
			<input name="CSECY<?php echo($user->ID); ?>" type="text" value="<?php echo($output); ?>" />
		<?php
	}

}

?>