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
    
    <?php the_post_thumbnail(); ?>
    <div class="entry-content">
        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'corto' ) ); ?>
        <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'corto' ), 'after' => '</div>' ) ); ?>
    </div><!-- .entry-content -->
    
    <?php endif; ?>

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
    <script src='http://connect.facebook.net/en_US/all.js'></script>
	<a style="cursor: pointer" onclick="postToFeed({ link: '<?php the_permalink() ?>', caption: '<?php the_title(); ?>', description: '<?php echo strip_tags(get_the_content() ) ?>', picture: '<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) ); ?>' }); return false;">
		<img src="/wp/wp-content/themes/corto/images/fb-share.png"/>
	</a>
    <script> 

	if (typeof (postToFeed)!="function") {
	  FB.init({appId: "433454403372693", status: true, cookie: true});
      function postToFeed(params) {

        // calling the API ...
        var obj = {
          method: 'feed',
          link: params.link,
          picture: params.picture,
          name: 'Facebook Dialogs',
          caption: params.caption,
          description: params.description
        };

        function callback(response) {
          return null;
        }

        FB.ui(obj, callback);
      }
  	}
    
    </script>
    		</div>
    	</div>
<!--
        <a href="#"><img src="/corto/wp/wp-content/themes/corto/images/add_twitter.png" /></a>
        <a href="#"><img src="/corto/wp/wp-content/themes/corto/images/add_fb.png" /></a>
        <a href="#"><img src="/corto/wp/wp-content/themes/corto/images/add_pin.png" /></a>
-->
    </footer><!-- #entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
