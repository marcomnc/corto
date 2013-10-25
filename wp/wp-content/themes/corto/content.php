<?php
/**
 * @package Corto
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'corto' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

        <?php if ( 'post' == get_post_type() ) : ?>
        <div class="entry-meta">
            <?php corto_posted_on(); ?>
        </div><!-- .entry-meta -->
        <?php endif; ?>

        <div class="clear"></div>

    </header><!-- .entry-header -->

    <?php if ( is_search() ) : // Only display Excerpts for Search ?>
    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->
    <?php else : ?>
    
    <?php the_post_thumbnail('post-thumbnail', array('mps-aync-img-loading' => true) ); ?>
    <div class="entry-content">
        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'corto' ) ); ?>
        <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'corto' ), 'after' => '</div>' ) ); ?>
    </div><!-- .entry-content -->
    
    <?php endif; ?>
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
    
    <footer class="entry-meta social-link">
        <div class="btn-social pintrest">
            <div class="social-set" onclick="pin_click('<?php echo $socialUrl;?>', '<?php echo $socialTitle;?>' + '\n' + '<?php echo $socialDescr;?>', '<?php echo $socialImg;?>')"></div>
        </div>
        <div class="btn-social twitter">
            <div class="social-set" onclick="tws_click('<?php echo $socialUrl;?>', '<?php echo $socialDescrShort;?>')"></div>
        </div>
        <div class="btn-social facebook">
            <div class="social-set" onclick="fbs_click('<?php echo $socialUrl;?>','<?php echo $socialImg;?>','<?php echo $socialTitle;?>', '<?php echo $socialTitle;?>', '<?php echo $socialDescr;?>');"></div>
        </div>
    
    <script> 
        
    </script>
    		
<!--
        <a href="#"><img src="/corto/wp/wp-content/themes/corto/images/add_twitter.png" /></a>
        <a href="#"><img src="/corto/wp/wp-content/themes/corto/images/add_fb.png" /></a>
        <a href="#"><img src="/corto/wp/wp-content/themes/corto/images/add_pin.png" /></a>
-->
    </footer><!-- #entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
