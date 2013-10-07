<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Corto
 * @since Corto 0.1
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />

<?php

function dev4press_debug_page_request() {
  global $wp, $template;
  define("D4P_EOL", "\r\n");
 
  echo '<!-- Request: ';
  echo empty($wp->request) ? "None" : esc_html($wp->request);
  echo ' -->'.D4P_EOL;
  echo '<!-- Matched Rewrite Rule: ';
  echo empty($wp->matched_rule) ? None : esc_html($wp->matched_rule);
  echo ' -->'.D4P_EOL;
  echo '<!-- Matched Rewrite Query: ';
  echo empty($wp->matched_query) ? "None" : esc_html($wp->matched_query);
  echo ' -->'.D4P_EOL;
  echo '<!-- Loaded Template: ';
  echo basename($template);
  echo ' -->'.D4P_EOL;
}

//dev4press_debug_page_request();

function dev4press_debug_rewrite_rules() {
  global $wp_rewrite;
  define("D4P_EOL", "\r\n");
  
  echo '<!--'.D4P_EOL;
  if (!empty($wp_rewrite->rules)) {
    echo 'Rewrite Rules'.D4P_EOL;
    foreach ($wp_rewrite->rules as $name => $value) {
      echo '- '.$name.': '.$value.D4P_EOL;
    }
  } else {
    echo 'No rules defined.'.D4P_EOL;
  }
  echo '-->';
}

//dev4press_debug_rewrite_rules();

?>

<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'corto' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>
<!--
<?php echo "<pre>";
//print_r(Mage::app()->getStore()); 
echo "</pre>"?>
-->
<!-- MAGENTO -->
<?php the_block('head'); ?>

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.twitter.js" type="text/javascript"></script>

</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed wrapper">
<?php do_action( 'before' ); ?>
    
    
    
	<header id="branding" role="banner">
<?php //Marco Precarico Magento 
//ini_set('display_errors', 1);
//define("MAGE_BASE_DIR", "..".DIRECTORY_SEPARATOR);
//require_once(MAGE_BASE_DIR.'app/Mage.php'); //Path to Magento
//umask(0);
//Mage::app();
//echo "PRovo...";
//if (Mage::GetSingleton("customer/session")->isLoggedIn())
//echo "SI - ". MAge::GetModel("customer/customer")->getCollection()->count();
//else
//echo "NO";
?>

            
            <?php the_block("header"); ?>
<?php // Marco Aggiunto la barra laterale da magento
      //		<script>
	//			// � necessario specificare l'url di partenza, per ora metot il negozio inglese
	//			var baseUrl="http://www.corto.com/preview/en/";
	//		</script>
?>            
			
            <?php //the_block("right_nav");?>
<?php // Fine Marco 
?>            
            <?php /*
		<hgroup>
			<h1 id="site-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
		</hgroup>

		<nav id="access" role="navigation">
			<h1 class="assistive-text section-heading"><?php _e( 'Main menu', 'corto' ); ?></h1>
			<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'corto' ); ?>"><?php _e( 'Skip to content', 'corto' ); ?></a></div>

			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</nav><!-- #access -->
             * 
             */ ?>
		
		
	</header><!-- #branding -->

<div id="main" class="wrapper-page-blog">
<div class="page">
  <div class="main-container col2-left-layout">  
    <div class="main">
