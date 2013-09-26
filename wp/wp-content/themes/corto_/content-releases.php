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
        <?php the_content(); ?>
        <!-- HERE -->
        
        <div class="clear"></div>
        
	<!-- START COMPOSITE DOWNLOAD -->
      <?php if(count(get_field('composite_download'))>0): ?> 
      <?php
          $_composite = array();
          $_composite_download = get_field('composite_download');
          for ($i=0;$i<count($_composite_download);$i++)
          {
          	if ($_composite_download[$i]['thumbnail']!='')
                $_composite[] = $_composite_download[$i]['thumbnail']; 
          }
      ?>
      <?php if (count($_composite)>0): ?>
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
        <div class="clear"></div>
      </div>
      <?php endif; ?>
      <?php endif; ?>

      <?php if(count(get_field('single_download'))>0): ?>
      <?php
	      $_files = array();
	      $_single_download = get_field('single_download');
	      for ($i=0;$i<count($_single_download);$i++)
	      {
	      	if ($_single_download[$i]['file']!='')
	            $_files[] = $_single_download[$i]['file']; 
	      }
	  ?>
	  <?php if (count($_files)>0): ?>
      <?php echo '<!-- SINGLE DOWNLOAD '; print_r(get_field('single_download')); echo '-->'; ?>      
      <h2 class="collection-files-title">DOWNLOADS</h2>
      <ul class="collection-files">

      <?php while(the_repeater_field('single_download')): ?>
      
      <?php
      
      $fileName = '';
      $parts = explode('/', get_sub_field('file'));
      if (count($parts)>0)
      	$fileName = $parts[count($parts)-1];
      
      ?>
      <li><a href="<?php the_sub_field('file'); ?>" target="_blank"><?php echo $fileName ?></a></li>
      
      <?php endwhile; ?>
      </ul>
      <?php endif; ?>
      <?php endif; ?>
	<!-- END COMPOSITE DOWNLOAD -->

        <div class="clear"></div>
        
            <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'corto' ), 'after' => '</div>' ) ); ?>
        
    </div><!-- .entry-content -->


</article><!-- #post-<?php the_ID(); ?> -->
