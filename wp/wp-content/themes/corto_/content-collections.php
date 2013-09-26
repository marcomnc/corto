<?php
/**
 * @package Corto
 */
?>





        


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        
        
        <div class="clear"></div>

    </header><!-- .entry-header -->

    <div class="entry-content">
        
      <?php
      //composite_download
      
      ?>
      <?php if(get_field('composite_download')): ?>
      <?php $rowCounter = 0; ?>
      
      <div class="collection-items">
      <?php while(the_repeater_field('composite_download')): ?>

      <div class="collection-item <?php if($rowCounter==3): ?>last<?php endif; ?>">
        <img src="<?php the_sub_field('thumbnail'); ?>" />
        <div class="collection-item-footer">
          <a class="collection-low" href="<?php the_sub_field('low-res'); ?>" target="_blank">Low-Res</a>
          <a class="collection-high" href="<?php the_sub_field('hi-res'); ?>" target="_blank">Hi-Res</a>
          <div class="clear"></div>
        </div>
      </div>
        
        <?php if($rowCounter==3): $rowCounter=0; ?>
        <div class="clear"></div>
        <?php else: ?>
        <?php $rowCounter++; ?>
        <?php endif; ?>

      <?php endwhile; ?>
      <?php endif; ?>
      
      
        <div class="clear"></div>
      </div>
      
      <h2 class="collection-files-title">DOWNLOADS</h2>
      <ul class="collection-files">
      <?php while(the_repeater_field('single_download')): ?>
      
      <?php
      
      $fileName = '';
      $parts = explode('/', get_sub_field('file'));
      if (count($parts)>0)
      {
      	$fileName = $parts[count($parts)-1];
      }
      
      echo '<!--';
      //print_r($parts);
     	echo '-->';
      
      ?>
      <li><a href="<?php the_sub_field('file'); ?>" target="_blank"><?php echo $fileName ?></a></li>
      
      <?php endwhile; ?>
      </ul>
      
      
    </div><!-- .entry-content -->


</article><!-- #post-<?php the_ID(); ?> -->
