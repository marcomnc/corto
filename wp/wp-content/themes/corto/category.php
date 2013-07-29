<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

		<section id="primary" class="col-main">
			<div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
                  
                  <div class="breadcrumbs">
                    <ul>
                      <li><a href="<?php echo home_url( '/' ); ?>">News</a> / </li>
                      <li class="last"><?php printf( __( '%s', 'corto' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></li>
                    </ul>
                  </div>
                  
                  <?php corto_content_nav( 'nav-above' ); ?>
                  <div class="clear"></div>
                </header>

				

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

				<?php endwhile; ?>

				<?php /*corto_content_nav( 'nav-below' );*/ ?>

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'corto' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'corto' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>

			</div><!-- #content -->
		</section><!-- #primary -->

<?php get_sidebar(); ?>
        <div class="clear"></div>
<?php get_footer(); ?>