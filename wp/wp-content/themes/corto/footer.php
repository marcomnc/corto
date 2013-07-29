<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Corto
 * @since Corto 0.1
 */
?>

    </div><!-- .main -->
  </div><!-- .main-container -->
</div><!-- .page -->
</div><!-- #main -->

	<footer id="colophon" role="contentinfo">
            <?php the_block("footer"); ?>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>