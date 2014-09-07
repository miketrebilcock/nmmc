<?php
/*
Template Name: NSBR Archive
*/

get_header(); ?>
<div id="content" class="widecolumn">
    <h1><?php single_cat_title(); ?></h1>
    <div><?php echo the_category_rss() ?></div>
    <?php if (have_posts()) : while (have_posts()) : the_post();?>
    <div class="post">
        <h2 id="post-<?php the_ID(); ?>"><?php the_title();?></h2>
        <div class="entrytext">
            <?php the_content('<p class="serif">Read the rest of this page »</p>'); ?>
            <a href="<?php echo get_permalink( ); ?>">Full Details</a>
        </div>
        <div>
        <?php 
            if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
                echo '<a href="'.get_permalink ().'>">'.the_post_thumbnail().'</a>';
            } 
        ?> 
        </div>
    </div>
    <?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>