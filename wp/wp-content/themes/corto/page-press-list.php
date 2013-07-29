<?php
/**
 * Template Name: Press Releases landing page
 * Description: Releases list with pagination
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

<?php
  $wp_query = null;
  $wp_query = new WP_Query();
  get_posts(array());

  $q = array(
      'post_type'=>'releases',
      'paged'=>$paged,
      'showposts'=>'10'
  );
  $wp_query->query($q); 
  
?>

<div id="primary" class="col-main">
  <div id="content" role="main">

    <header class="page-header">
        <div class="breadcrumbs">
          <ul>
            <li>Press releases</li>
          </ul>
        </div>
      
        <?php corto_content_nav( 'nav-above' ); ?>
      
    </header>
    <div class="clear"></div>
              

    
    <?php if ( ! have_posts() ) : ?>
    <div>
      <span>Oops, that's bad. No results.</span>
    </div><!-- #post-0 -->
    <?php endif; ?>
    
    
    <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

      <article>
        <div class="release-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>
        <div class="release-list-content">
          <div class="release-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
          <div class="release-date"><?php echo get_the_date(); ?></div>
          <div class="clear"></div>
          <div class="release-excerpt"><?php the_excerpt(); ?></div>
          <div class="clear"></div>
          <div class="release-more"><a href="<?php the_permalink(); ?>">MORE...</a></div>
        </div>
        <div class="clear"></div>
      </article>

    <?php endwhile; ?>
    <?php endif; ?>
    
    <?php corto_content_nav( 'nav-below' ); ?>


  </div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar('reserved'); ?>

<?php

$q = null;
$wp_query = null;

?>
        
        <div class="clear"></div>
<?php get_footer(); ?>