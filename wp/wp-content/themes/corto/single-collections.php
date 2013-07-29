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
<div style="clear: left; padding: 20px 10px 0px 0px; color: #333">
In this section you will be able to dowload text and images in LOW(res) and in HI(res).<br /><br />
If you would like to dowload all images please select the look book pdf at the top, HI res images have to be dowloaded separately.<br /><br /> 
Download may take a couple of minutes depending on the bandwidth, please be patient.<br /><br />
If you need different pictures or have special requests please do not hesitate to contact us <a href="mailto:press@corto.com">press@corto.com</a><br />
For price requests please request by email.<br /><br />Please always credit <span class="bold">www.corto.com</span> as our eshopping website delivers to most countries in the world.<br />
Thank you for your collaboration. 
</div>
        
     <div class="clear"></div>   
<?php get_footer(); ?>