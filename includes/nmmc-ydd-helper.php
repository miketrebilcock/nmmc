<?php

defined('ABSPATH') or die("No script kiddies please!");
require_once dirname( __FILE__ ) . '/mt-post-helper.php';
///////////////////////////////////////////
// Register Yacht Design Database Post Type
///////////////////////////////////////////
function ydd_post_type() {

    $labels = array(
    'name'                => _x( 'YDD', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Yacht Design', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Yacht Design', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Design:', 'text_domain' ),
		'all_items'           => __( 'All Yacht Designs', 'text_domain' ),
		'view_item'           => __( 'View Yacht Design', 'text_domain' ),
		'add_new_item'        => __( 'Add Yacht Design', 'text_domain' ),
		'add_new'             => __( 'Add Yacht Design', 'text_domain' ),
		'edit_item'           => __( 'Edit Yacht Design', 'text_domain' ),
		'update_item'         => __( 'Update Yacht Design', 'text_domain' ),
		'search_items'        => __( 'Search YDD', 'text_domain' ),
		'not_found'           => __( 'Yacht Design Not found in Register', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$capabilities = array(
		'edit_post'           => 'edit_ydd',
		'read_post'           => 'read_ydd',
		'delete_post'         => 'delete_ydd',
		'edit_posts'          => 'edit_ydd',
		'edit_others_posts'   => 'edit_others_ydd',
		'publish_posts'       => 'publish_ydd',
		'read_private_posts'  => 'read_private_ydd',
	);
	$args = array(
		'label'               => __( 'ydd', 'text_domain' ),
		'description'         => __( 'Yacht Design Database', 'text_domain' ),
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
	register_post_type( 'ydd', $args );
    
    add_ydd_capabilities_to_role( 'curator' );
    add_ydd_capabilities_to_role( 'administrator' );
    add_ydd_capabilities_to_role( 'nmmc_admin' );
}

function add_ydd_capabilities_to_role($role_name)
{
    $role = get_role( $role_name );
 
    $role->add_cap( 'read_ydd' );
	$role->add_cap( 'edit_ydd' );
	$role->add_cap( 'delete_ydd' );
	$role->add_cap( 'publish_ydd' );
	$role->add_cap( 'read_private_ydd' );
	$role->add_cap( 'edit_others_ydd' );
}

function ydd_create_taxonomies(){
    register_taxonomy(
        'designer', 'ydd',
        array(
            'hierarchical'=> true,
            'label' => 'Designers',
            'singular_label' => 'Designer',
            'rewrite' => true
        )
    );
    register_taxonomy(
        'boattype', 'ydd',
        array(
            'hierarchical'=> true,
            'label' => 'Boat Types',
            'singular_label' => 'Boat Type',
            'rewrite' => true
        )
    );
    register_taxonomy(
        'boatclass', 'ydd',
        array(
            'hierarchical'=> true,
            'label' => 'Boat Classes',
            'singular_label' => 'Boat Class',
            'rewrite' => true
        )
    );
        register_taxonomy(
        'magazine', 'ydd',
        array(
            'hierarchical'=> true,
            'label' => 'Magazines',
            'singular_label' => 'Magazine',
            'rewrite' => true
        )
    );
}

function ydd_add_custom_box() {
    add_meta_box(
        'ydd_details',
        __( 'Design Details', 'nmmc_ydd' ),
        'ydd_details_box',
        'ydd');
}

function ydd_details_box() {
	global $post;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'ydd_noncename' );
	?>
	<div id="meta_inner">
	<?php
    $ydd = new nmmc_ydd_helper();

    echo ('<label for="ydd_id">' . __( 'YDD ID', 'nmmc_ydd' ) . '</label>' );
    echo ('<input type="text" name="ydd_id" id="ydd_id" value="'.esc_attr( $ydd->get_ydd_id($post->ID)).'" />' );

	echo ('<label for="ydd_year_month">' . __( 'Year/Month', 'nmmc_ydd' ) . '</label>' );
    echo ('<input type="text" name="ydd_year_month" id="ydd_year_month" value="'.esc_attr( $ydd->get_ydd_year($post->ID)).'" />' );

    echo ('<label for="ydd_page">' . __( 'Page', 'nmmc_ydd' ) . '</label>' );
    echo ('<input type="text" name="ydd_page" id="ydd_page" value="'.esc_attr( $ydd->get_ydd_page($post->ID)).'" />' );
?>
	</div>
<?php

}

/* When the post is saved, saves our custom data */
function ydd_save_custom_data( $post_id ) {
    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['ydd_noncename'] ) )
        return $post_id;

    if ( !wp_verify_nonce( $_POST['ydd_noncename'], plugin_basename( __FILE__ ) ) )
        return $post_id;
        
    // Check this is the Contact Custom Post Type
    if ( 'ydd' != $_POST['post_type'] ) {
        return $post_id;
    }
        
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_ydd' ) ) return $post_id;

    $ydd = new nmmc_ydd_helper();
    // OK to save meta data
    
    $ydd_year_month = sanitize_text_field( $_POST['ydd_year_month'] );
    $ydd->save_ydd_year_month($post_id,$ydd_year_month);

    
    $ydd_page = sanitize_text_field( $_POST['ydd_page'] );
    $ydd->save_ydd_page($post_id,$ydd_page);
    
    $ydd_id = sanitize_text_field( $_POST['ydd_id'] );
    $ydd->save_ydd_id($post_id,$ydd_id);
}

class nmmc_ydd_helper {
    
    function get_current_hash_for_post($post_id){
        return get_post_meta( $post_id, '_ydd_hash', true );
        
    }
    
    function get_ydd_post_id($ydd_id)
    {
    	$ydd_id = preg_replace("/[^0-9]+/i", "", $boat_id);
			$rd_args = array(
				'post_type' => 'ydd',
				'meta_query' => array(
									array(
										'key' => '_ydd_id',
										'value' => $ydd_id,
										)
									)
						);
			$rd_query = new WP_Query( $rd_args );
			
			if ( $rd_query->have_posts() )
				return $rd_query->post->ID;
	}
    
    function get_ydd_id($post_id)
    {
        return get_post_meta( $post_id, '_ydd_id', true );
    }
    
    function save_ydd_id($post_id, $ydd_id)
    {
        return update_post_meta( $post_id, '_ydd_id', $ydd_id );
    }
    
    function get_ydd_year($post_id)
    {
        return get_post_meta( $post_id, '_ydd_year', true );
    }
    
    function save_ydd_year($post_id, $ydd_year)
    {
        return update_post_meta( $post_id, '_ydd_year', $ydd_year );
    }
    
    function get_ydd_page($post_id)
    {
        return get_post_meta( $post_id, '_ydd_page', true );
    }
    
    function save_ydd_page($post_id, $ydd_page)
    {
        return update_post_meta( $post_id, '_ydd_page', $ydd_page );
    }
    
    function process_ydd_CSV_import($data)
    {
		
		echo '<li>';
				
		$post = array();
		$meta = array();
		$tax = array();

		$is_update = false;
		$error = new WP_Error();
		
		if($data[0])
		{
			$meta['_ydd_id'] = $data[0];
			$meta['_ydd_hash'] = md5(implode(",", $data));
			$post['post_type'] = "ydd";
			$post_id = $this->get_ydd_post_id($meta['_ydd_id']);
			
			if($post_id)
			{
				$post['post_ID']=$post_id;
				$is_update = true;
				$current_hash = $yd->get_current_hash_for_post($post_id);
			
				echo esc_html(sprintf(__('Found Design: %s ', 'nmmc-csv-importer'), $meta['_ydd_id']));
				if($current_hash==$meta['_ydd_hash'])
				{
					echo 'No Updated Needed.';
					return;
				}
			}else{
				echo esc_html(sprintf(__('Design is new: %s ', 'nmmc-csv-importer'), $meta['_ydd_id']));
			}
			
			$post['post_status'] = "publish";
						
			// (string) post title
			$post_title = $data[4];
			if ($post_title) {
				$post['post_title'] = $post_title;
			}
			
			$tax['magazine'] = $data[1];
			$meta['_ydd_issue'] = $data[2];
			$meta['_ydd_page'] = $data[3];
			$tax['designer'] = $data[5];
			$tax['boattype']= $data[6];
			$tax['boatclass'] = $data[7];
            
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
	}
}


// Hook into the 'init' action
add_action( 'init', 'ydd_post_type', 0 );
add_action( 'init', 'ydd_create_taxonomies', 0);
add_action( 'add_meta_boxes', 'ydd_add_custom_box' );
add_action( 'save_post', 'ydd_save_custom_data' );

?>