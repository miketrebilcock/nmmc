<?php

defined('ABSPATH') or die("No script kiddies please!");
require_once dirname( __FILE__ ) . '/mt-post-helper.php';
//////////////////////////
// Register NSBR Post Type
//////////////////////////
function nsbr_post_type() {

    $labels = array(
		'name'                => _x( 'NSBR', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Boat', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'NSBR', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Boat:', 'text_domain' ),
		'all_items'           => __( 'All Small Boat Registrations', 'text_domain' ),
		'view_item'           => __( 'View Boat Registration', 'text_domain' ),
		'add_new_item'        => __( 'Add Small Boat Registration', 'text_domain' ),
		'add_new'             => __( 'Add Small Boat Registration', 'text_domain' ),
		'edit_item'           => __( 'Edit Boat Registration', 'text_domain' ),
		'update_item'         => __( 'Update Boat Registration', 'text_domain' ),
		'search_items'        => __( 'Search NSBR', 'text_domain' ),
		'not_found'           => __( 'Boat Not found in Register', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$capabilities = array(
		'edit_post'           => 'edit_nsbr',
		'read_post'           => 'read_nsbr',
		'delete_post'         => 'delete_nsbr',
		'edit_posts'          => 'edit_nsbr',
		'edit_others_posts'   => 'edit_others_nsbr',
		'publish_posts'       => 'publish_nsbr',
		'read_private_posts'  => 'read_private_nsbr',
	);
	$args = array(
		'label'               => __( 'nsbr', 'text_domain' ),
		'description'         => __( 'National Small Boat Register', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions',),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
	    'capabilities'        => $capabilities,
	);
	register_post_type( 'nsbr', $args );
	
	add_role('nsbr_manager', 'NSBR Manager', array(
    'edit_nsbr' => true,
    'edit_others_nsbr' => true,
    'read_nsbr' => true,
    'publish_nsbr' => true,
    'read_private_nsbr' => true,
    'delete_nsbr' => true,
    'read'=> true
     ));
     
	  add_nsbr_capabilities_to_role( 'curator' );
    add_nsbr_capabilities_to_role( 'administrator' );
    add_nsbr_capabilities_to_role( 'nmmc_admin' );
}

function add_nsbr_capabilities_to_role($role_name)
{
    $role = get_role( $role_name );
 
    $role->add_cap( 'read_nsbr' );
	$role->add_cap( 'edit_nsbr' );
	$role->add_cap( 'delete_nsbr' );
	$role->add_cap( 'publish_nsbr' );
	$role->add_cap( 'read_private_nsbr' );
	$role->add_cap( 'edit_others_nsbr' );
}

function nsbr_create_taxonomies(){
    register_taxonomy(
        'boatclass', 'nsbr',
        array(
            'hierarchical'=> true,
            'label' => 'Boat Classes',
            'singular_label' => 'Boat Class',
            'rewrite' => true
        )
    );
    register_taxonomy(
        'boatfunction', 'nsbr',
        array(
            'hierarchical'=> true,
            'label' => 'Boat Functions',
            'singular_label' => 'Boat Function',
            'rewrite' => true
        )
    );
    register_taxonomy(
        'boatlocation', 'nsbr',
        array(
            'hierarchical'=> true,
            'label' => 'Boat Locations',
            'singular_label' => 'Boat Location',
            'rewrite' => true
        )
    );
        register_taxonomy(
        'boat_current_use', 'nsbr',
        array(
            'hierarchical'=> true,
            'label' => 'Boat Usages',
            'singular_label' => 'Boat Usage',
            'rewrite' => true
        )
    );
}

function nsbr_add_custom_box() {
    add_meta_box(
        'nsbr_details',
        __( 'Boat Details', 'myplugin_textdomain' ),
        'nsbr_boat_details_box',
        'nsbr');
    add_meta_box(
        'nsbr_history',
        __( 'Boat History', 'myplugin_textdomain' ),
        'nsbr_boat_history_box',
        'nsbr');
    add_meta_box(
        'nsbr_gallery',
        __( 'Boat Photos', 'myplugin_textdomain' ),
        'nsbr_boat_gallery_box',
        'nsbr');
}

function nsbr_boat_details_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'nsbr_noncename' );
	?>
	<div id="meta_inner">
	<?php
    $boat = new nmmc_nsbr_helper();
    
	echo ('<label for="nsbr_reg_no">' . __( 'NSBR Registration No', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_reg_no" id="nsbr_reg_no" value="'.esc_attr( $boat->get_boat_nsbr_registration($post->ID)).'" />' );
	
	echo ('<label for="nsbr_length_m">' . __( 'Length (m)', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_length_m" id="nsbr_length_m" value="'.esc_attr( $boat->get_boat_length_m($post->ID) ).'" />' );
    
	echo ('<label for="nsbr_length_ft">' . __( 'Length (ft)', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_length_ft" id="nsbr_length_ft" value="'.esc_attr( $boat->get_boat_length_ft($post->ID) ).'" />' );
    
	echo ('<label for="nsbr_breadth_m">' . __( 'Breadth (m)', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_breadth_m" id="nsbr_breadth_m" value="'.esc_attr( $boat->get_boat_breadth_m($post->ID) ).'" />' );
    
	echo ('<label for="nsbr_breadth_ft">' . __( 'Breadth (ft)', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_breadth_ft" id="nsbr_breadth_ft" value="'.esc_attr( $boat->get_boat_breadth_ft($post->ID) ).'" />' );

	echo ('<label for="nsbr_build_date">' . __( 'Build Date', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_build_date" id="nsbr_build_date" value="'.esc_attr($boat->get_boat_build_date($post->ID) ).'" />' );

	echo ('<label for="nsbr_copyright">' . __( 'Copyright', 'nmmc_nsbr' ) . '</label>' );
    echo ('<input type="text" name="nsbr_copyright" id="nsbr_copyright" value="'.esc_attr( $boat->get_boat_copyright($post->ID) ).'" />' );
?>
	</div>
<?php

}

function nsbr_boat_history_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'nsbr_noncename' );
	?>
	<div id="meta_inner">
	<?php
    $boat = new nmmc_nsbr_helper();
	//get the saved meta as an array
	$history = $boat->get_boat_history($post->ID);

	$c = 0;
	if ( is_array( $history ) ) {
    	foreach( $history as $record ) {
        	if ( isset( $record['Year'] ) || isset( $record['year'] ) ) {
            	printf( '<p>Year <input type="text" name="history[%1$s][year]" value="%2$s" /> -- Change : <input type="text" name="history[%1$s][change]" value="%3$s" /> -- Source : <input type="text" name="history[%1$s][source]" value="%4$s" /><span class="remove">%5$s</span></p>', $c, $record['year'], $record['change'], $record['source'], __( 'Remove Change' ) );
            $c = $c +1;
        	}
    	}
	}
	?>
	<input id="history_count" type="hidden" value="<?php $c ?>" />
	<span id="here"></span>
	<span class="add"><?php _e('Add Change'); ?></span>
</div>
<?php

}

function nsbr_boat_gallery_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'nsbr_noncename' );
	?>
	<div id="meta_inner">
	<?php

	//get the saved meta as an array
    $boat = new nmmc_nsbr_helper();
	$gallery = $boat->get_boat_gallery($post->ID);

	$image_count = 0;
	if ( is_array( $gallery ) ) {
    	foreach( $gallery as $image ) {
        	if ( isset( $image['id'] ) ) {
        		$image_src = wp_get_attachment_url( $image['id'] );
            	printf( '<div><image src="%1$s" /><input type="hidden" name="gallery[%2$s][id]" value="%3$s" /><span class="removeimage">%4$s</span></div>', $image_src, $image_count, $image['id'], __( 'Remove Image' ) );
            	$image_count = $image_count +1;
        	}
    	}
	}
	?>
	<input id="image_count" type="hidden" value="<?php $image_count ?>" />
	<span id="newimagehere"></span>
	<span class="addimage"><?php _e('Add Photo'); ?></span>
</div>
<?php

}

/* When the post is saved, saves our custom data */
function nsbr_save_custom_data( $post_id ) {
    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['nsbr_noncename'] ) )
        return $post_id;

    if ( !wp_verify_nonce( $_POST['nsbr_noncename'], plugin_basename( __FILE__ ) ) )
        return $post_id;
        
    // Check this is the Contact Custom Post Type
    if ( 'nsbr' != $_POST['post_type'] ) {
        return $post_id;
    }
        
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_nsbr' ) ) return $post_id;

    $boat = new nmmc_nsbr_helper();
    // OK to save meta data
    $history = $_POST['history'];
    $boat->save_boat_history($post_id, $history);
    
    $gallery = $_POST['gallery'];
    $boat->save_boat_gallery($post_id,$gallery);
    
    $nsbr_reg_no = sanitize_text_field( $_POST['nsbr_reg_no'] );
    $boat->save_boat_nsbr_registration($post_id,$nsbr_reg_no);

    
    $length_m = sanitize_text_field( $_POST['nsbr_length_m'] );
    update_post_meta( $post_id, '_nsbr_length_m', $length_m );
    
    $length_ft = sanitize_text_field( $_POST['nsbr_length_ft'] );
    update_post_meta( $post_id, '_nsbr_length_ft', $length_ft );
    
    $breadth_m = sanitize_text_field( $_POST['nsbr_breadth_m'] );
    update_post_meta( $post_id, '_nsbr_breadth_m', $breadth_m );
    
    $breadth_ft = sanitize_text_field( $_POST['nsbr_breadth_ft'] );
    update_post_meta( $post_id, '_nsbr_breadth_ft', $breadth_ft );
    
    $location = sanitize_text_field( $_POST['nsbr_location'] );
    update_post_meta( $post_id, '_nsbr_location', $location );
    
    $current_use = sanitize_text_field( $_POST['nsbr_current_use'] );
    update_post_meta( $post_id, '_nsbr_current_use', $current_use );
    
    $build_date = sanitize_text_field( $_POST['nsbr_build_date'] );
    update_post_meta( $post_id, '_nsbr_build_date', $build_date );
    
    $copyright = sanitize_text_field( $_POST['nsbr_copyright'] );
    update_post_meta( $post_id, '_nsbr_copyright', $copyright );
}

function nsbr_admin_scripts() {
	global $post_type;
    if( 'nsbr' == $post_type )
    {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('jquery');
		wp_register_script('nsbr', plugins_url( '/js/nsbr-admin.js', __FILE__ ),
array('jquery','media-upload','thickbox'));
		wp_enqueue_script('nsbr');
    }
}

function nsbr_admin_styles() {
	global $post_type;
	if( 'nsbr' == $post_type )
    {
		wp_enqueue_style('thickbox');
		wp_register_style('nsbr', plugins_url( '/css/nsbr-admin.css', __FILE__ ));
		wp_enqueue_style('nsbr');
    }
}

add_action('admin_print_scripts', 'nsbr_admin_scripts');
add_action('admin_print_styles', 'nsbr_admin_styles');

// Hook into the 'init' action
add_action( 'init', 'nsbr_post_type', 0 );
add_action( 'init', 'nsbr_create_taxonomies', 0);
add_action( 'add_meta_boxes', 'nsbr_add_custom_box' );
add_action( 'save_post', 'nsbr_save_custom_data' );

class nmmc_nsbr_helper {
    
    function get_current_hash_for_post($post_id){
        return get_post_meta( $post_id, '_nsbr_boat_hash', true );
        
    }
    
    function get_boat_post_id($boat_id)
    {
		$boat_id = preg_replace("/[^0-9]+/i", "", $boat_id);
			echo esc_html(sprintf(__('Looking for Boat Id: %s', 'nmmc-csv-importer'), $boat_id));
			//Find the boat
			$rd_args = array(
				'post_type' => 'nsbr',
				'meta_query' => array(
									array(
										'key' => '_nsbr_reg_no',
										'value' => $boat_id,
										)
									)
						);
			$rd_query = new WP_Query( $rd_args );
			
			if ( $rd_query->have_posts() )
				return $rd_query->post->ID;
	}
    
    function get_boat_nsbr_registration($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_reg_no', true );
    }
    
    function save_boat_nsbr_registration($post_id,$nsbr_reg_no)
    {
        return update_post_meta( $post_id, '_nsbr_reg_no', $nsbr_reg_no );
    }
    
    function get_boat_length_m($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_length_m', true );
    }
    
    function get_boat_length_ft($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_length_ft', true );
    }
    
     function get_boat_breadth_m($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_breadth_m', true );
    }
    
    function get_boat_breadth_ft($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_breadth_ft', true );
    }
    
    function get_boat_build_date($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_build_date', true );
    }
    
    function get_boat_copyright($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_copyright', true );
    }
    
    function get_boat_history($post_id)
    {
        return get_post_meta( $post_id, 'history', true );
    }
    
    function save_boat_history($post_id, $history)
    {
        update_post_meta($post_id,'history',$history);
    }
    
    function get_boat_history_hash($post_id)
    {
        return get_post_meta( $post_id, '_nsbr_boat_history_hash', true );
    }
    
    function save_boat_history_hash($post_id, $hash)
    {
        update_post_meta($post_id,'_nsbr_boat_history_hash',$hash);
    }
    
    function get_boat_gallery($post_id)
    {
        return get_post_meta( $post_id, 'gallery', true );
    }
    
    function save_boat_gallery($post_id, $gallery)
    {
        update_post_meta($post_id,'gallery',$gallery);
    }
    
        
	function process_nsbr_main_post($h, $data, $boat_id)
	{
		echo '<li>';
				
		$post = array();
		$meta = array();
		$tax = array();

		$is_update = false;
		$error = new WP_Error();
				
 		if ($boat_id) {
 			
 			$meta['_nsbr_reg_no'] = $boat_id;
			$meta['_nsbr_boat_hash'] = md5(implode(",", $data));
			$post['post_type'] = "nsbr";
			
			$post_id = $this->get_boat_post_id($boat_id);
			
			if ( $post_id )  {
				$post['post_ID']=$post_id;
				$is_update = true;
				$current_hash = $this->get_current_hash_for_post($post_id);
				
				echo esc_html(sprintf(__('Found Boat: %s ', 'nmmc-csv-importer'), $boat_id));
				if($current_hash==$meta['_nsbr_boat_hash'])
				{
					echo 'No Updated Needed.';
					return;
				}
			}else{
				echo esc_html(sprintf(__('Boat is new: %s ', 'nmmc-csv-importer'), $boat_id));
			}
			
			$post['post_status'] = "publish";
						
			// (string) post title
			$post_title = $h->get_data($this,$data,'Name');
			if ($post_title) {
				$post['post_title'] = $post_title;
			}
			
			// (string) post content
			$post_content = $h->get_data($this,$data,'Web history');
			if ($post_content) {
				$post['post_content'] = $post_content;
			}
			
			// (string) post thumbnail image uri
			$post_thumbnail = $h->get_data($this,$data,'Photo');
				
			if ($post_thumbnail)
			{
				//echo esc_html(sprintf(__('Loading thumbnail: %s ', 'nmmc-csv-importer'), 'http://www.nmmc.co.uk/images/uploaded/nsbr/'.$post_thumbnail));
				$post_thumbnailcontent = remote_get_file('http://www.nmmc.co.uk/images/uploaded/nsbr/'.$post_thumbnail,'', $post_title.'_');
			}

			if( is_wp_error($post_thumbnailcontent))
			{
				//echo esc_html(sprintf(__('Unable to Load thumbnail: %s', 'nmmc-csv-importer'), $post_thumbnail));
				$error_message = $post_thumbnailcontent->get_error_message();
				echo esc_html(sprintf(__('Error: "%s"', 'nmmc-csv-importer'), $error_message));
				$error->add( 'unable_to_load_thumbnail', sprintf(__('Error Loading Thumbnail %s.', 'nmmc-csv-importer'), $error_message));
			}
			else
			{
				//echo esc_html(sprintf(__('Loaded thumbnail: %s', 'nmmc-csv-importer'), $post_thumbnail));
				//echo esc_html(sprintf(__('Thumbnail: %s', 'nmmc-csv-importer'), $post_thumbnailcontent));
			}
				
			$boat_class = $h->get_data($this,$data,'Class');
			if ($boat_class) {
				$tax['boatclass'] = $boat_class;
			}
			
			$boat_function = $h->get_data($this,$data,'Function');
			if ($boat_function) {
				$tax['boatfunction'] = $boat_function;
			}
			
			$boat_length_m = $h->get_data($this,$data,'Length M');
			if ($boat_length_m) {
				$meta['_nsbr_length_m'] = $boat_length_m;
			}
			
			$boat_length_ft = $h->get_data($this,$data,'Length Ft');
			if ($boat_length_ft) {
				$meta['_nsbr_length_ft'] = $boat_length_ft;
			}
			
			$boat_breadth_m = $h->get_data($this,$data,'Breadth M');
			if ($boat_breadth_m) {
				$meta['_nsbr_breadth_m'] = $boat_breadth_m;
			}
			
			$boat_breadth_ft = $h->get_data($this,$data,'Breadth Ft');
			if ($boat_breadth_ft) {
				$meta['_nsbr_breadth_ft'] = $boat_breadth_ft;
			}
			
			$boat_build_date = $h->get_data($this,$data,'Build date');
			if ($boat_build_date) {
				$meta['_nsbr_build_date'] = $boat_build_date;
			}
			
			$boat_location = $h->get_data($this,$data,'Place');
			if ($boat_location) {
				$tax['boatlocation'] = $boat_location;
			}
			
			$boat_current_use = $h->get_data($this,$data,'Usage');
			if ($boat_current_use) {
				$tax['boat_current_use'] = $boat_current_use;
			}
			
			$boat_copyright = $h->get_data($this,$data,'Copyright');
			if ($boat_copyright) {
				$meta['_nsbr_copyright'] = $boat_copyright;
			}
							
			if (!$error->get_error_codes() && $dry_run == false) {
				
					$mt = new mt_post_helper();
					$result = $mt->save_post($post,$meta,$tax,$post_thumbnail,$is_update);
				
				if ($result) {
					echo esc_html(sprintf(__('Processing %s done.', 'nmmc-csv-importer'), $post_title));
				} else {
					$error->add( 'save_post', __('An error occurred while saving the post to database.', 'nmmc-csv-importer') );
				}
			}
 		}
		// show error messages
		foreach ($error->get_error_messages() as $message) {
			echo esc_html($message).'<br>';
		}
		
		echo '</li>';
	}

	function store_nsbr_history($h, $data, $history_cache)
	{
		$history_entry = array();
		$error = new WP_Error();

		$boat_id = $h->get_data($this,$data,'BoatID');
				
 		if ($boat_id) {
 			$history_entry['year'] = $h->get_data($this,$data,'Year');
 			$history_entry['change'] = $h->get_data($this,$data,'Change');
 			$history_entry['source'] = $h->get_data($this,$data,'Source');
 			
 			if($history_cache[$boat_id])
 			{
 				$length = $history_cache[$boat_id]["History"].length;
 				$history_cache[$boat_id]["History"][$length] = $history_entry;
 			}else{
 				$history_cache[$boat_id]["BoatID"] = $boat_id;
 				$history_cache[$boat_id]["History"][0] = $history_entry;
 			}
 		}
 		
 		return $history_cache;
	}
    
    
}

?>