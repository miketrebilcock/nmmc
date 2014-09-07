<?php
/*
	Plugin Name: NMMC
	Plugin URI:
	Description: National Maritime Museum Cornwall customisations
	Version: 0.2
	Author: First Class Web Design
	Author URI: http://www.firstclasswebdesign.co.uk/
*/
defined('ABSPATH') or die("No script kiddies please!");

define( 'CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//----------------------------------------------
//--------------add theme support for thumbnails
//----------------------------------------------
if ( function_exists( 'add_theme_support')){
    add_theme_support( 'post-thumbnails' );
}
add_image_size( 'admin-list-thumb', 80, 80, true); //admin thumbnail


//////////////////////////
// Register News Post Type
//////////////////////////
function news_post_type() {

	$labels = array(
		'name'                => _x( 'News', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'News', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'News', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent News Item:', 'text_domain' ),
		'all_items'           => __( 'All News Items', 'text_domain' ),
		'view_item'           => __( 'View News', 'text_domain' ),
		'add_new_item'        => __( 'Add News', 'text_domain' ),
		'add_new'             => __( 'Add News', 'text_domain' ),
		'edit_item'           => __( 'Edit News', 'text_domain' ),
		'update_item'         => __( 'Update News', 'text_domain' ),
		'search_items'        => __( 'Search News', 'text_domain' ),
		'not_found'           => __( 'News Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$capabilities = array(
		'edit_post'           => 'edit_news',
		'read_post'           => 'read_news',
		'delete_post'         => 'delete_news',
		'edit_posts'          => 'edit_news',
		'edit_others_posts'   => 'edit_others_news',
		'publish_posts'       => 'publish_news',
		'read_private_posts'  => 'read_private_news',
	);
	$args = array(
		'label'               => __( 'news', 'text_domain' ),
		'description'         => __( 'News Items', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor','excerpt', 'thumbnail', 'revisions', ),
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
	register_post_type( 'news', $args );
     
    add_news_capabilities_to_role( 'marketing' );
	add_news_capabilities_to_role( 'administrator' );
    add_news_capabilities_to_role( 'nmmc_admin' );
}

function add_news_capabilities_to_role($role_name)
{
    $role = get_role( $role_name );
 
    // add "organize_gallery" to this role object
	$role->add_cap( 'read_news' );
	$role->add_cap( 'edit_news' );
	$role->add_cap( 'delete_news' );
	$role->add_cap( 'publish_news' );
	$role->add_cap( 'read_private_news' );
	$role->add_cap( 'edit_others_news' );
}


// Hook into the 'init' action
add_action( 'init', 'news_post_type', 0 );



/**
 * Checks if a particular user has a role.
 * Returns true if a match was found.
 *
 * @param string $role Role name.
 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool
 */
function check_user_role( $role, $user_id = null ) {
 
    if ( is_numeric( $user_id ) )
	$user = get_userdata( $user_id );
    else
        $user = wp_get_current_user();
 
    if ( empty( $user ) )
	return false;
 
    return in_array( $role, (array) $user->roles );
}


///////////////////////////////
// Hide Jetpack from non-admins
///////////////////////////////

function jetpack_hide_from_authors() {

    if ( ! check_user_role( 'administrator' ) ) {
        remove_menu_page( 'jetpack' );
    }
}

add_action('jetpack_admin_menu', 'jetpack_hide_from_authors');
include( plugin_dir_path( __FILE__ ) . 'includes/nmmc-roles.php');
include( plugin_dir_path( __FILE__ ) . 'includes/admin-dashboard.php');
include( plugin_dir_path( __FILE__ ) . 'includes/template-choose.php');
include( plugin_dir_path( __FILE__ ) . 'includes/nmmc-nsbr-helper.php');
include( plugin_dir_path( __FILE__ ) . 'includes/nmmc-ydd-helper.php');
include( plugin_dir_path( __FILE__ ) . 'includes/nmmc-csv-importer.php');
?>
