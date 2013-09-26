<?php
/**
 * Template Name: Events landing page
 * Description: A template with a custom list of upcoming events
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

<div id="primary">
  <div id="content" role="main">
    
    <div id="secondary" class="widget-area col-left sidebar" role="complementary">
      <div id="menu">
        <div class="container">
            <aside class="widget module">
                <h1 class="widget-title"><a href="#">Private previews</a></h1>
            </aside>
        </div><!-- .container -->
      </div><!-- #menu -->
    </div><!-- #secondary .widget-area -->
    <div class="clear"></div>

<?php
  $wp_query = null;
  $wp_query = new WP_Query();
  get_posts(array());

    
  $querydate = date('Y/m/d');
  $q = array(
      'post_type'=>'event',
      'paged'=>1,
      'showposts'=>'3',
      'orderby'=>'meta_value',
      'meta_key'=>'event_date',
      'meta_value'=>$querydate,
      'meta_compare'=>'>=',
      'order'=>'ASC'
  );
  $wp_query->query($q); 
  
?>
    <?php if ( ! have_posts() ) : ?>
    <div>
      <span>No events scheduled. Please <a href="#">subscribe</a> to our newsletter.</span>
    </div><!-- #post-0 -->
  <?php endif; ?>


  <?php 
    if (have_posts()) : 
    $post_count = $wp_query->post_count;
    $counter = 0;
  ?>
  <div class="events events-<?php echo $post_count; ?>-column">
  <?php while (have_posts()) : the_post(); $counter++; ?>
    
    <article class="event-item<?php if ($counter == $post_count): ?> event-item-last<?php endif; ?>">
      <?php

      if (get_field('event_date'))
      {
        $_dbDate = get_field('event_date'); // Y-m-d
        $_dbDate = str_replace('/', '-', $_dbDate);
        $dbDate = strtotime($_dbDate);
      }

      ?>
      <img src="<?php echo get_field('image_'.$post_count.'_column') ?>" />
      <div class="event-title">
        <?php the_title(); ?><br />
        <?php echo date_i18n('d F Y', $dbDate); ?>
      </div>
      
      <div class="event-content">
        <?php the_content(); ?>
      </div>
      
    </article>
    
  <?php endwhile; ?>
  </div><!-- .events -->  
  <?php endif; ?>
    
    <div class="clear"></div>

  </div><!-- #content -->
</div><!-- #primary -->

        
        
        
        
        <div class="clear"></div>
<?php get_footer(); ?>