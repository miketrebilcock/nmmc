<?php
/**
 * @package NMMC
 * @subpackage Theme - YDD
 */
require_once CD_PLUGIN_PATH. 'includes/nmmc-ydd-helper.php';
get_header();
?>

    <div id="content" class="widecolumn" role="main">
    Yacht Design Database
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
                <div class="ydd designer">
                    <?php the_terms( $post->ID, 'designer', 'Designer: ', ', ', ' ' );?>
                </div>
                <div class="ydd boattype">
                    <?php the_terms( $post->ID, 'boattype', 'Type: ', ', ', ' ' );?>
                </div>
                <div class="ydd boat_class">
                    <?php the_terms( $post->ID, 'boatclass', 'Class: ', ', ', ' ' );?>
                </div>
                <div class="ydd magazine">
                    <?php the_terms( $post->ID, 'magazine', 'Magazine: ', ', ', ' ' ); ?>
                </div>
                <?php $ydd = new nmmc_ydd_helper(); ?>
                <div class="ydd registration">Yacht Design Id: <?php echo esc_attr( $ydd->get_ydd_id($post->ID)); ?></div>
                <div class="ydd edition">Edition: <?php echo esc_attr( $ydd->get_ydd_year($post->ID)); ?></div>
                <div class="ydd page">Page: <?php echo esc_attr( $ydd->get_ydd_page($post->ID)); ?></div>
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
