<?php

defined('ABSPATH') or die("No script kiddies please!");

if ( !defined('WP_LOAD_IMPORTERS') )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}

// Load Helpers
require dirname( __FILE__ ) . '/nmmc-csv-helper.php';
require_once dirname( __FILE__ ) . '/nmmc-nsbr-helper.php';
require_once dirname( __FILE__ ) . '/nmmc-ydd-helper.php';
/**
 * CSV Importer
 *
 * @package WordPress
 * @subpackage Importer
 */
if ( class_exists( 'WP_Importer' ) ) {
class NMMC_CSV_Importer extends WP_Importer {
	
	/** Sheet columns
	* @value array
	*/
	public $column_indexes = array();
	public $column_keys = array();

 	// User interface wrapper start
	function header() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>'.__('NMMC Data Importer', 'nmmc-csv-importer').'</h2>';
	}

	// User interface wrapper end
	function footer() {
		echo '</div>';
	}
	
	// Step 1
	function greet() {
		echo '<h3>'.__( 'Select a valid NMMC datafile to import', 'nmmc-csv-importer' ).'</h3>';
		wp_import_upload_form( add_query_arg('step', 1) );
	//	echo '<h3>'.__( 'NSBR Import boat history', 'nmmc-csv-importer' ).'</h3>';
	//	wp_import_upload_form( add_query_arg('step', 1) );
	}

	// Step 2
	function import() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'nmmc-csv-importer' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';
			return false;
		} else if ( ! file_exists( $file['file'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'nmmc-csv-importer' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'nmmc-csv-importer' ), esc_html( $file['file'] ) );
			echo '</p>';
			return false;
		}
		
		$this->id = (int) $file['id'];
		$this->file = get_attached_file($this->id);
		$result = $this->process_posts();
		if ( is_wp_error( $result ) )
			return $result;
	}

	// process parse csv ind insert posts
	function process_posts() {
		$h = new NMMC_CSV_Helper;

		$handle = $h->fopen($this->file, 'r');
		if ( $handle == false ) {
			echo '<p><strong>'.__( 'Failed to open file.', 'nmmc-csv-importer' ).'</strong></p>';
			wp_import_cleanup($this->id);
			return false;
		}
		
		$is_first = true;
		
		echo '<ol>';
		$mode = 'UNKNOWN';
		$history_cache = array();
		$yd = new nmmc_ydd_helper();
		$boat = new nmmc_nsbr_helper();
		while (($data = $h->fgetcsv($handle)) !== FALSE) {
			if ($is_first) {
				$h->parse_columns( $this, $data );
				$is_first = false;
				
				if(in_array("BoatID",$this->column_keys) &&
				in_array("Name",$this->column_keys) &&
				in_array("Class",$this->column_keys) &&
				in_array("Function",$this->column_keys) &&
				in_array("Length M",$this->column_keys) &&
				in_array("Length Ft",$this->column_keys) &&
				in_array("Breadth M",$this->column_keys) &&
				in_array("Breadth Ft",$this->column_keys) &&
				in_array("Build date",$this->column_keys) &&
				in_array("Place",$this->column_keys) &&
				in_array("Usage",$this->column_keys) &&
				in_array("Web history",$this->column_keys) &&
				in_array("Photo",$this->column_keys) &&
				in_array("Pic",$this->column_keys) &&
				in_array("Pic2",$this->column_keys) &&
				in_array("Pic3",$this->column_keys) &&
				in_array("Pic4",$this->column_keys) &&
				in_array("Copyright",$this->column_keys)
				)
				{
					echo "<h3>NSBR Main Import</h3>";
					$mode="nsbr-main";
				}else if(in_array("BoatID",$this->column_keys) &&
				in_array("Year",$this->column_keys) &&
				in_array("Change",$this->column_keys) &&
				in_array("Source",$this->column_keys)
				)
				{
					echo "<h3>NSBR History Import</h3>";
					$mode="nsbr-history";
				}else if(count($this->column_keys)==8)
				{
					echo "<h3>Yacht Design Database Import</h3>";
					$mode="ydd";
					$yd->process_ydd_CSV_import($data);
				}
				else
				{
					echo "<h3>File not recognised.</h3>";
					echo "Heading Count: ".count($this->column_keys);
					echo "Found the following headings:<br/>";
					var_dump ($data);
				}
				
			} else {
				switch ($mode) {
					case 'nsbr-main' :
						$boat_id = $h->get_data($this,$data,'BoatID');
						$boat->process_nsbr_main_post($h, $data, $boat_id);
						add_option("import-nsbr-main", date('Y-m-d H:i:s'), null, $autoload);
					break;
						
					case 'nsbr-history':
						$history_cache = $boat->store_nsbr_history($h, $data, $history_cache);
						add_option("import-nsbr-history", date('Y-m-d H:i:s'), null, $autoload);
					break;
					
					case 'ydd':
						$yd->process_ydd_CSV_import($data);
					break;
				}
			}
		}
		
		if($mode='nsbr-history')
		{
			foreach( $history_cache as $boat_history ) {
				$post_id = $boat->get_boat_post_id($boat_history["BoatID"]);
				if($post_id)
				{
					$history_hash = $boat->get_boat_history_hash($post_id);
					$new_hash = md5(implode(",", $boat_history["History"]));
					if($history_hash!=$new_hash)
					{
						$boat->save_boat_history($post_id, $boat_history["History"]);
						$boat->save_boat_history_hash($post_id, $new_hash);
						echo "<li>Updated history for boat ".$boat_history["BoatID"].".</li>";
					}else{
						echo "<li>Found history for boat ".$boat_history["BoatID"].", but no change to history.</li>";
					}
				}else{
					echo "<li>Found history for boat ".$boat_history["BoatID"].", but boat registration not found.</li>";
				}
			}
		}
		
		echo '</ol>';

		$h->fclose($handle);
		
		switch ($mode) {
					case 'nsbr-main' :
						add_option("import-nsbr-main", date('Y-m-d H:i:s'), null, $autoload);
					break;
						
					case 'nsbr-history':
						add_option("import-nsbr-history", date('Y-m-d H:i:s'), null, $autoload);
					break;
					
					case 'ydd':
						add_option("import-ydd", date('Y-m-d H:i:s'), null, $autoload);
					break;
				}
		
		wp_import_cleanup($this->id);
		
		echo '<h3>'.__('All Done.', 'nmmc-csv-importer').'</h3>';
	}
	
	


	// dispatcher
	function dispatch() {
		$this->header();
		
		if (empty ($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];

		switch ($step) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-upload');
				set_time_limit(0);
				$result = $this->import();
				if ( is_wp_error( $result ) )
					echo $result->get_error_message();
				break;
		}
		
		$this->footer();
	}
	
}

// setup importer
$nmmc_csv_importer = new NMMC_CSV_Importer();

register_importer('csv', __('NMMC', 'nmmc-csv-importer'), __('Import NMMC Files.', 'nmmc-csv-importer'), array ($nmmc_csv_importer, 'dispatch'));

} // class_exists( 'WP_Importer' )
