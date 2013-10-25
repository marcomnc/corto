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
    	 <?php            
            $socialTitle = get_the_title(get_the_ID());        
            $socialUrl = get_permalink(get_the_ID());
            $socialDescr = urlencode(nl2br(strip_tags(get_the_content(""))));
            $socialDescrShort = substr($socialDescr, 0, 100);
            if (strlen($socialDescr)> 100):
                $socialDescrShort .= "...";
            endif;
            $socialImg = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
        ?>
        
        <div class="btn-social pintrest">
            <div class="social-set" onclick="pin_click('<?php echo $socialUrl;?>', '<?php echo $socialTitle;?>' + '\n' + '<?php echo $socialDescr;?>', '<?php echo $socialImg;?>')"></div>
        </div>
        <div class="btn-social twitter">
            <div class="social-set" onclick="tws_click('<?php echo $socialUrl;?>', '<?php echo $socialDescrShort;?>')"></div>
        </div>
        <div class="btn-social facebook">
            <div class="social-set" onclick="fbs_click('<?php echo $socialUrl;?>','<?php echo $socialImg;?>','<?php echo $socialTitle;?>', '<?php echo $socialTitle;?>', '<?php echo $socialDescr;?>');"></div>
        </div>
    </footer><!-- .entry-meta -->
