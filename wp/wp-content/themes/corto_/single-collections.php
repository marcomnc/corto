<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

<div id="primary" class="col-main">
    <div id="content" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

        <div class="breadcrumbs">
          <ul><li>Collections</li></ul>
        </div>
        <?php /*corto_content_nav( 'nav-above' );*/ ?>
        <div class="clear"></div>

        <?php get_template_part( 'content', 'collections' ); ?>

        <?php
            // If comments are open or we have at least one comment, load up the comment template
            if ( comments_open() || '0' != get_comments_number() )
                    comments_template( '', true );
        ?>

    <?php endwhile; // end of the loop. ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar('reserved'); ?>
        
        <div class="clear"></div>
<?php get_footer(); ?>