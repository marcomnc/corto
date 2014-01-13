<?php
/**
 * The Sidebar containing the menu for media section.
 *
 * @package Corto
 * @since Corto 0.1
 */
?>
		<div id="secondary" class="widget-area col-left sidebar" role="complementary">
          
          <div id="menu">
            <div class="container">
<?php $url = get_option( 'siteurl' ); ?>              
              <aside class="widget module">
					<h1 class="widget-title"><a href="<?php echo "$url/media/"?>">Media home</a></h1>
				</aside>
                <aside class="widget module">
					<h1 class="widget-title"><a href="<?php echo "$url/press-releases/"?>">Press releases</a></h1>
				</aside>
                <aside class="widget module">
					<h1 class="widget-title"><a href="<?php echo "$url/collections/"?>">Collections</a></h1>
				</aside>
                <aside class="widget module">
					<h1 class="widget-title"><a href="<?php echo "$url/brand-assets/"?>">Brand assets</a></h1>
				</aside>
              
            </div><!-- .container -->
          </div><!-- #menu -->
              
		</div><!-- #secondary .widget-area -->
