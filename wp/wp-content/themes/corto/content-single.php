<?php
/**
 * @package Corto
 */
?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php corto_posted_on(); ?>
        
        <div class="clear"></div>

    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php the_content(); ?>
        
            <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'corto' ), 'after' => '</div>' ) ); ?>
        
    </div><!-- .entry-content -->
    
    <div class="entry-content">
       <?php if(get_field('gallery')): ?>
       <?php    $rowCounter = 0; ?>
      
        <div class="collection-items">
            <?php while(the_repeater_field('gallery')): ?>

            <div class="collection-item  <?php if($rowCounter==3): ?>last<?php endif; ?>">
                <a class="thumbnail" href="<?php the_sub_field('zoom'); ?>" rel="gallery">
                    <img src="<?php the_sub_field('thumbnail'); ?>" />              
                </a>
            </div>
        
            <?php if($rowCounter==3): $rowCounter=0; ?>
            <div class="clear"></div>
            <?php endif; ?>

        <?php   $rowCounter++; endwhile; ?>
        </div>

        <?php endif; ?>
    </div>
    
</article><!-- #post-<?php the_ID(); ?> -->
<?php if(get_field('gallery')): ?>
<script>
    $j(document).ready(function() {
        $j("a.thumbnail").fancybox({
            autoScale   : true,
            showCloseButton : true,
            onComplete: function() {
                $j('#fancybox-content').css({backgroundColor: '#fff'});
            	$j("#fancybox-close").html("CLOSE");
            }            
        });       
    });
</script>
<?php endif; ?>
<div class="clear"></div>
    <footer class="entry-meta social-link">
    	<div class="btn-social pintrest">
    		<div class="social-set"></div>
    		<div class="social-real" style="display:none">
    			<a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink() ?>&media=<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) ); ?>&description=<?php echo strip_tags(get_the_content() ) ?>" class="pin-it-button" count-layout="none"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
    		</div>
    	</div>

    	<div class="btn-social twitter">
    		<div class="social-set"></div>    		
    		<div class="social-real" style="display:none">
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink() ?>" data-text="<?php echo strip_tags(get_the_content() ) ?>" data-count="none">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    		</div>
    	</div>
    	<div class="btn-social facebook">
    		<div class="social-set"></div>
    		<div class="social-real" style="display:none">
	<div id='fb-root'></div>
<!--    <script src='http://connect.facebook.net/en_US/all.js'></script>-->
<!--	<a href="#" onclick="postToFeed({ link: '<?php the_permalink() ?>', caption: '<?php the_title(); ?>',description: '<?php echo str_replace(chr(13).chr(10), " ", nl2br(strip_tags(get_the_content(""))));?>', picture: '<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) ); ?>' }); return false;">-->
	<a id="fb-share-link" href="http://www.facebook.com/dialog/feed?
          app_id=433454403372693&amp;
          link=<?php the_permalink() ?>&amp;
          picture=<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) ); ?>&amp;
          name=<?php the_title(); ?>&amp;
          caption=<?php the_title(); ?>&amp;
          description=<?php echo str_replace(chr(13).chr(10), " ", nl2br(strip_tags(get_the_content(""))));?>&amp;
	  redirect_uri=<?php echo 'http://www.corto.com/en/window-close'; ?>" 
	onClick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,');return false;">
		<img src="/wp/wp-content/themes/corto/images/fb-share.png"/>
	</a>
    <script> 

    </script>
    		</div>
    	</div>
    </footer><!-- .entry-meta -->
