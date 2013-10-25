<?php
/**
 * Corto functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Corto
 * @since Corto 0.1
 */

$functions_path = TEMPLATEPATH . '/functions/';
$includes_path = TEMPLATEPATH . '/includes/';

//Theme initialization
require_once $includes_path . 'theme-init.php';

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'corto_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override corto_setup() in a child theme, add your own corto_setup to your child theme's
 * functions.php file.
 */
function corto_setup() {
	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on corto, use a find and replace
	 * to change 'corto' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'corto', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'corto' ),
	) );

	/**
	 * Add support for the Aside and Gallery Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'gallery' ) );
        
        // This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );
        
}
endif; // corto_setup

/**
 * Tell WordPress to run corto_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'corto_setup' );

/**
 * Set a default theme color array for WP.com.
 */
$themecolors = array(
	'bg' => 'ffffff',
	'border' => 'eeeeee',
	'text' => '444444',
);

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function corto_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'corto_page_menu_args' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function corto_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar 1', 'corto' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );

	register_sidebar( array(
		'name' => __( 'Sidebar 2', 'corto' ),
		'id' => 'sidebar-2',
		'description' => __( 'An optional second sidebar area', 'corto' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'init', 'corto_widgets_init' );

if ( ! function_exists( 'corto_content_nav' ) ):
/**
 * Display navigation to next/previous pages when applicable
 *
 * @since Corto 1.2
 */
function corto_content_nav( $nav_id ) {
	global $wp_query;

	?>
	<nav id="<?php echo $nav_id; ?>" class="entry-nav">
		<h1 class="assistive-text section-heading"><?php _e( 'Post navigation', 'corto' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>
		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '', 'Previous post link', 'corto' ) . '</span> Previous<!--%title-->' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', 'Next<!--%title--> <span class="meta-nav">' . _x( '', 'Next post link', 'corto' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>
		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( 'Older posts', 'corto' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'corto' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo $nav_id; ?> -->
	<?php
}
endif; // corto_content_nav


if ( ! function_exists( 'corto_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own corto_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Corto 0.4
 */
function corto_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'corto' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'corto' ), ' ' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 40 ); ?>
					<?php printf( __( '%s <span class="says">says:</span>', 'corto' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><?php _e( 'Your comment is awaiting moderation.', 'corto' ); ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-meta commentmetadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'corto' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( '(Edit)', 'corto' ), ' ' );
					?>
				</div><!-- .comment-meta .commentmetadata -->
			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for corto_comment()

if ( ! function_exists( 'corto_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own corto_posted_on to override in a child theme
 *
 * @since Corto 1.2
 */
function corto_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s" pubdate>%3$s</time></a>', 'corto' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}
endif;


/**
 * Adds custom classes to the array of body classes.
 *
 * @since Corto 1.2
 */
function corto_body_classes( $classes ) {
	// Adds a class of single-author to blogs with only 1 published author
	if ( ! is_multi_author() ) {
		$classes[] = 'single-author';
	}

	return $classes;
}
add_filter( 'body_class', 'corto_body_classes' );

/**
 * Returns true if a blog has more than 1 category
 *
 * @since Corto 1.2
 */
function corto_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so corto_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so corto_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in corto_categorized_blog
 *
 * @since Corto 1.2
 */
function corto_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'corto_category_transient_flusher' );
add_action( 'save_post', 'corto_category_transient_flusher' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function corto_enhanced_image_navigation( $url ) {
	global $post, $wp_rewrite;

	$id = (int) $post->ID;
	$object = get_post( $id );
	if ( wp_attachment_is_image( $post->ID ) && ( $wp_rewrite->using_permalinks() && ( $object->post_parent > 0 ) && ( $object->post_parent != $id ) ) )
		$url = $url . '#main';

	return $url;
}
add_filter( 'attachment_link', 'corto_enhanced_image_navigation' );

//add_filter('redirect_canonical','corto_disable_redirect_canonical');
 
//function corto_disable_redirect_canonical( $redirect_url ) {
    //if ( is_singular())
	//    $redirect_url = false;
//    return true;
//}

add_filter('wp_get_attachment_image_attributes', 'corto_get_attachment_image_attributes');

function corto_get_attachment_image_attributes( $attr, $attachment ){
    
    if (isset($attr['mps-aync-img-loading']) && $attr['mps-aync-img-loading']) {
        $ret = array();
        foreach ($attr as $k => $v) {
            if ($k == 'mps-aync-img-loading') {
                continue;
            }
            
            if ($k == 'src') {
                $ret['mps-aync-img-loading'] = $v;
                $ret[$k] = get_template_directory_uri() ."/images/attachment-ajax-loader.gif";
                continue;
            }
            
            if ($k == 'class') {
                $ret[$k] = "$v mps-aync-img-loading";
                continue;
            }
            
            $ret[$k] = $v;
        }

        return $ret;

    }
    
    return $attr;
    
}

function corto_the_events_header() {
    
    $html  ='<div id="corto-events-clippings">';
    $html .= corto_get_the_clipping_slide();
    $html .= corto_get_the_events_title();    
    $html .='</div>';
    
    echo $html;
 
}

function corto_get_the_clipping_slide() {
    $q = array(
        'post_type'=>'press',        
        'showposts'=>'20'
    );
    
    $press = new WP_Query($q);

    $html = "";
    if ($press) {
        
        $html .= '<div class="corto-home-clippings-container">';
        $html .= '<div class="corto-home-clippings">';
        
        foreach ($press->posts as $p) {

            $html .= '<div class="cuttings-item">';
            $html .= '<a href="'. get_site_url() . '/press-cuttings/" class="cuttings-item-link" id="item2"><img src="' . get_field('thumbnail', $p->ID) . '" title="' . $p->post_title . '"></a>';
            $html .= '</div>';
        }
        $html .= "</div>";
        $html .= '<a href="'. get_site_url() . '/press-cuttings/" class="corto-more-cuttings">' . Mage::Helper('autelcorto')->__('Show more Cuttings') . '</a>';
        $html .= '<div class="clarer"></div>';
        $html .= "</div>";
    }
    
    return $html;
}

function corto_get_the_events_title() {
    $querydate = date('Y/m/d');
    $q = array(
        'post_type'=>'event',
        'paged'=>1,
        'showposts'=>'3',
        'orderby'=>'meta_value',
        'meta_key'=>'event_date',
        'meta_value'=>$querydate,
        'meta_compare'=>'>=',
        'order'=>'ASC'
    );

    $events = new WP_Query( $q );
      
    $html = "";
    if ( $events ) {
        the_post();
        $html  .='<div class="event-title">' . $events->post->post_title . '</div>';
    } else {
        $html  .= '<span>We are currently preparing our upcoming  events, to find out more and to be notified in advance  please <a class="class-popuplogin" href="#">subscribe</a> to our newsletter.</span>';
    }
    return $html;
}

/**
 * This theme was built with PHP, Semantic HTML, CSS, love, and a Corto.
 */
