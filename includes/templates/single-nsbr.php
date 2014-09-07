<?php
/**
 * @package NMMC
 * @subpackage Theme - NSBR
 */

get_header();
?>

    <div id="content" class="widecolumn" role="main">
    National Small Boat Register
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link( '%link', '&laquo; %title' ) ?></div>
			<div class="alignright"><?php next_post_link( '%link', '%title &raquo;' ) ?></div>
		</div>

		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
 
			<div class="entry">
            <div class="span12">
                <div class="span9">
    				<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'nmmc_nsbr') . '</p>'); ?>
                </div>
                <div class="span3">
                <?php 
                    if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
                        the_post_thumbnail();   
                    } 
                ?>
                </div>
            </div>    
            <div class="span12">
                <div class="nsbr current_use">
                    <?php the_terms( $post->ID, 'boat_current_use', 'Current Use: ', ', ', ' ' );?>
                </div>
                <div class="nsbr boat_location">
                    <?php the_terms( $post->ID, 'boatlocation', 'Location: ', ', ', ' ' );?>
                </div>
                <div class="nsbr boat_class">
                    <?php the_terms( $post->ID, 'boatclass', 'Class: ', ', ', ' ' );?>
                </div>
                <div class="nsbr boat_function">
                    <?php the_terms( $post->ID, 'boatfunction', 'Function: ', ', ', ' ' ); ?>
                </div>
                <?php 
                $nsbr_reg_no = get_post_meta( $post->ID, '_nsbr_reg_no', true );
                $length_m = get_post_meta( $post->ID, '_nsbr_length_m', true );
                $length_ft = get_post_meta( $post->ID, '_nsbr_length_ft', true );
                $breadth_m = get_post_meta( $post->ID, '_nsbr_breadth_m', true );
                $build_date = get_post_meta( $post->ID, '_nsbr_build_date', true );
                $copyright = get_post_meta( $post->ID, '_nsbr_copyright', true );
                ?>
                               
                <div class="nsbr registration">NSBR Regisatration No: <?php echo esc_attr( $nsbr_reg_no ); ?></div>
                <div class="nsbr size">Length(m): <?php echo esc_attr( $length_m ); ?></div>
                <div class="nsbr size">Length(ft): <?php echo esc_attr( $length_ft ); ?></div>				
                <div class="nsbr size">Breadth(m): <?php echo esc_attr( $breadth_m ); ?></div>
                <div class="nsbr size">Breadth(ft): <?php echo esc_attr( $breadth_ft ); ?></div>
                <div class="nsbr year">Build Date: <?php  echo esc_attr( $build_date ); ?></div>
                <div class="nsbr copyright">Copyright: <?php  echo esc_attr( $copyright ); ?></div>
            </div>    
                <div id="nsbr history span12">
                	<?php
                
                	//get the saved meta as an arry
                	$history = get_post_meta($post->ID,'history',true);
                
                	if ( is_array( $history ) ) {
                    	foreach( $history as $record ) {
                        	if ( isset( $record['Year'] ) || isset( $record['year'] ) ) {
                            	printf( '<div class="span3">Year: %1$s </div> <div class="span3">Change: %2$s</div> <div class="span3">Source : %3$s</div>', $record['year'], $record['change'], $record['source']);
                                                   	}
                    	}
                	}
                	?>
                </div>
                
                <div class="nsbr gallery span12">
                    <?php
                
                	//get the saved meta as an arry
                	$gallery = get_post_meta($post->ID,'gallery',true);
                

                	if ( is_array( $gallery ) ) {
                    	foreach( $gallery as $image ) {
                        	if ( isset( $image['id'] ) ) {
                        		$image_src = wp_get_attachment_url( $image['id'] );
                            	printf( '<div class="span3"><image src="%1$s" /></div>', $image_src );
                        	}
                    	}
                	}
                	?>
                </div>
                <p class="postmetadata alt">
					<small>
						<?php /* This is commented, because it requires a little adjusting sometimes.
							You'll need to download this plugin, and follow the instructions:
							http://binarybonsai.com/wordpress/time-since/ */
							/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); $time_since = sprintf(__('%s ago', 'nmmc_nsbr'), time_since($entry_datetime)); */ ?>
						<?php printf(__('This entry was posted on %1$s at %2$s and is filed under %3$s.', 'nmmc_nsbr'), get_the_time(__('l, F jS, Y', 'nmmc_nsbr')), get_the_time(), get_the_category_list(', ')); ?>
						<?php printf(__("You can follow any responses to this entry through the <a href='%s'>RSS 2.0</a> feed.", "nsbr"), get_post_comments_feed_link()); ?> 

						<?php if ( comments_open() && pings_open() ) {
							// Both Comments and Pings are open ?>
							<?php printf(__('You can <a href="#respond">leave a response</a>, or <a href="%s" rel="trackback">trackback</a> from your own site.', 'nmmc_nsbr'), get_trackback_url()); ?>

						<?php } elseif ( !comments_open() && pings_open() ) {
							// Only Pings are Open ?>
							<?php printf(__('Responses are currently closed, but you can <a href="%s" rel="trackback">trackback</a> from your own site.', 'nmmc_nsbr'), get_trackback_url()); ?>

						<?php } elseif ( comments_open() && !pings_open() ) {
							// Comments are open, Pings are not ?>
							<?php _e('You can skip to the end and leave a response. Pinging is currently not allowed.', 'nmmc_nsbr'); ?>

						<?php } elseif ( !comments_open() && !pings_open() ) {
							// Neither Comments, nor Pings are open ?>
							<?php _e('Both comments and pings are currently closed.', 'nmmc_nsbr'); ?>

						<?php } edit_post_link(__('Edit this entry', 'nmmc_nsbr'),'','.'); ?>

					</small>
				</p>

			</div>
		</div>

	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.', 'nmmc_nsbr'); ?></p>

    <?php endif; ?>

	</div>

<?php get_footer(); ?>
