<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

<div class="col-main">
	<div class="std">
		<p style="text-align: center;"><img style="display: block; margin-left: auto; margin-right: auto;" src="<?php echo get_template_directory_uri(); ?>/images/ops-404.gif" alt="" /></p>
		<div style="text-align: center;">
			<p><strong><span style="font-size: large;">Whoops, our bad...</span></strong></p>
			<p>&nbsp;</p>
		</div>
	</div>
</div>
<?php get_footer(); ?>