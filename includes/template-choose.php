<?php

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/
 
if ( ! defined( 'RC_TC_BASE_FILE' ) )
    define( 'RC_TC_BASE_FILE', __FILE__ );
if ( ! defined( 'RC_TC_BASE_DIR' ) )
    define( 'RC_TC_BASE_DIR', dirname( RC_TC_BASE_FILE ) );
if ( ! defined( 'RC_TC_PLUGIN_URL' ) )
    define( 'RC_TC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
 
/*
|--------------------------------------------------------------------------
| PLUGIN FUNCTIONS
|--------------------------------------------------------------------------
*/
 
/**
 * Returns template file
 *
 * @since 1.0
 */
 
function rc_tc_template_chooser( $template ) {
 
    // Post ID
    $post_id = get_the_ID();
    $post_type = get_post_type( $post_id );
    echo ('<!-- Have Post Type ' . __( $post_type, 'nmmc' ) . '-->' );
    // For all other CPT
    if ( $post_type != 'nsbr' && $post_type !='ydd' ) {
        echo ('<!-- Unknown Type returning ' . __( $template, 'nmmc' ) . '-->' );
        return $template;
    }
 
    // Else use custom template
    if ( is_single() ) {
        echo ('<!-- Is Single returning ' . __( 'single-'.$post_type, 'nmmc' ) . '-->' );
        return rc_tc_get_template_hierarchy( 'single-'.$post_type);
    }else{
        return rc_tc_get_template_hierarchy( 'category-'.$post_type);
    }
    echo ('<!-- Returning nothing -->' );
    return $template;
}

/**
 * Get the custom template if is set
 *
 * @since 1.0
 */
 
function rc_tc_get_template_hierarchy( $template ) {
 
    echo ('<!-- Looking for Template ' . __( $template, 'nmmc' ) . '-->' );
    // Get the template slug
    $template_slug = rtrim( $template, '.php' );
    $template = $template_slug . '.php';
    echo ('<!-- Looking for Template ' . __( $template, 'nmmc' ) . '-->' );
 
 
    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'plugin_template/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = RC_TC_BASE_DIR . '/templates/' . $template;
    }
    echo ('<!-- Use Template ' . __( $file, 'nmmc' ) . '-->' );
    return apply_filters( 'rc_repl_template_' . $template, $file );
}
 
/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/
 
add_filter( 'template_include', 'rc_tc_template_chooser' );

?>