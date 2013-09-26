<?php
/**
 * Template Name: Collections landing page
 * Description: Template with a list of collections with downloads
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

<div id="primary" class="col-main">
  <div id="content" role="main">

    <header class="page-header">
        <div class="breadcrumbs">
          <ul>
            <li>Collections</li>
          </ul>
        </div>
    </header>
    <div class="clear"></div>
              
<?php
  $wp_query = null;
  $wp_query = new WP_Query();
  get_posts(array());

  $q = array(
      'post_type'=>'collections',
      'paged'=>1,
      'showposts'=>'10'
  );
  $wp_query->query($q); 
  
?>
    
    <?php if ( ! have_posts() ) : ?>
    <div>
      <span>Oops, that's bad. No results.</span>
    </div><!-- #post-0 -->
    <?php endif; ?>
    
    <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

      <article>
        <div class="collection-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>
        <div class="collection-list-content">
          <div class="collection-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
          <div class="collection-date"><?php echo get_the_date(); ?></div>
          <div class="clear"></div>
          <div class="collection-excerpt"><?php the_excerpt(); ?></div>
          <div class="clear"></div>
          <div class="collection-more"><a href="<?php the_permalink(); ?>">MORE...</a></div>
        </div>
        <div class="clear"></div>
      </article>

    <?php endwhile; ?>
    <?php endif; ?>

  </div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar('reserved'); ?>
        
        
        <div class="clear"></div>
<?php get_footer(); ?>