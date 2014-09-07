<?php

defined('ABSPATH') or die("No script kiddies please!");

add_action('wp_dashboard_setup', 'nmmc_dashboard_widgets');

  

function nmmc_dashboard_widgets() {

    global $wp_meta_boxes;
    wp_add_dashboard_widget('custom_help_widget', 'NMMC Wordpress', 'nmmc_dashboard_summary');

}

 

function nmmc_dashboard_summary() {
    echo '<p>National Maritime Museum Cornwall Wordpress! Need help? Contact the developer <a href="mailto:jock.turner@gmail.com">here</a>.</p>';
        echo '<ul>
	<li>Release Date: August 2014</li>
	<li>Author: <a href="http://www.firstclasswebdesign.co.uk/">First Class Web Design</a></li>
    <li><b>Last Database Imports:</b><ul>
        <li>NBSR Main: '.get_option('import-nsbr-main').' <a href="/wp-admin/admin.php?import=csv">Import Now</a></li>
        <li> NBSR History: '.get_option('import-nsbr-history').' <a href="/wp-admin/admin.php?import=csv">Import Now</a></li>
        <li>Yacht Designs: '.get_option('import-ydd').' <a href="/wp-admin/admin.php?import=csv">Import Now</a></li>
	</ul>';
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function remove_dashboard_widgets() {
    global $wp_meta_boxes;
	// Today widget
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	// Last comments
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	// Incoming links
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	// Plugins
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
}

// Custom WordPress Footer
function remove_footer_admin () {
    echo '&copy; 2014 - FirstClass Web Design ';
}
add_filter('admin_footer_text', 'remove_footer_admin');

/*------------------------------*/
/* ADD CUSTOM POST TYPES TO 'AT A GLANCE' WIDGET
/*------------------------------*/
add_action('dashboard_glance_items', 'add_custom_post_counts');
 
function add_custom_post_counts() {
   $post_types = array('nsbr','ydd'); // array of custom post types to add to 'At A Glance' widget
   foreach ($post_types as $pt) :
      $pt_info = get_post_type_object($pt); // get a specific CPT's details
      $num_posts = wp_count_posts($pt); // retrieve number of posts associated with this CPT
      $num = number_format_i18n($num_posts->publish); // number of published posts for this CPT
      $text = _n( $pt_info->labels->singular_name, $pt_info->labels->name, intval($num_posts->publish) ); // singular/plural text label for CPT
      echo '<li class="page-count '.$pt_info->name.'-count"><a href="edit.php?post_type='.$pt.'">'.$num.' '.$text.'</a></li>';
   endforeach;
}
?>