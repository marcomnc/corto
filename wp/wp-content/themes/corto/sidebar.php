<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Corto
 * @since Corto 0.1
 */
?>
<div id="secondary" class="widget-area col-left sidebar" role="complementary">
          
    <div id="menu">
        <div class="container">
            <?php do_action( 'before_sidebar' ); ?>
            <?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

            <aside id="archives" class="widget module">
                <h1 class="widget-title"><?php _e( 'Archives', 'corto' ); ?></h1>
                <ul><?php wp_get_archives( array( 'type' => 'monthly' ) ); ?></ul>
            </aside>

            <aside id="meta" class="widget module">
                <h1 class="widget-title"><?php _e( 'Meta', 'corto' ); ?></h1>
                <ul>
                    <?php wp_register(); ?>
                    <aside><?php wp_loginout(); ?></aside>
                    <?php wp_meta(); ?>
                </ul>
            </aside>

            <?php endif; // end sidebar widget area ?>
              
        </div><!-- .container -->
    </div><!-- #menu -->
          
<?php the_block("twitter.timeline"); ?>
              
</div><!-- #secondary .widget-area -->

<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
<div id="tertiary" class="widget-area" role="complementary">
    <?php dynamic_sidebar( 'sidebar-2' ); ?>
</div><!-- #tertiary .widget-area -->
<?php endif; ?>
