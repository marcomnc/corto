<?php
/**
 * Template Name: Media landing page
 * Description: Login / logout form for the reserved area
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header();
$uam = new UserAccessManager();

?>

		<div id="primary" class="col-main">
			<div id="content" role="main">
              
              <header class="page-header">
                  
                  <div class="breadcrumbs">
                    <ul>
                      <li>Media access</li>
                    </ul>
                  </div>

              </header>
              
              <?php if(is_user_logged_in()): ?>
<?php
$layout = jck_mwi::layout();
$block = $layout->getBlock('media-home')->toHtml().'';
?>
              <div class="media-logged">
                  <?php if ($block == "") : ?>  
                  Welcome, you are currenty logged as <span class="bold"><?php $usr = wp_get_current_user(); echo $usr->data->display_name; ?></span>.
                  <br />Choose a section from the left navigation bar and download reserved media.<br />
                  Every image is available in both standard and high resolution format.<br /><br />
                  If you need more pictures, texts or videos, please contact us.<br /><br />
                  <a class="btn" href="<?php echo wp_logout_url( get_permalink() ); ?>">LOGOUT</a>
                  <!--
                  <form action="<?php echo get_bloginfo('wpurl'); ?>/wp-login.php" method="GET">
                    <input type="hidden" name="action" value="logout" />
                    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                    <input class="btn" type="submit" name="wp-submit" value="LOGOUT" />
                  </form>
                  -->
                  <?php else: ?>
                  <?php echo $block; ?>
                  <?php endif; ?>
              </div>
              
              
              <?php else: ?>

              <div class="media-content">
                <div class="media-left">
                  <h2>Corto media pass</h2>
                  <p>
                    This area has<br />
                    restricted access<br />
                    limited to accredited press
                  </p>
                </div>
                <div class="media-center">
                  <img src="<?php echo get_template_directory_uri() ?>/images/media_access_logo.png" title="Media access" />
                  <br/>
                  <h2>My Corto Media pass</h2>
                  <?php
                  
                  //echo $uam->getLoginBarHtml();
                  
                  /*
                   * 
                   * 
   
    $loginForm = '<form action="'.$loginUrl.'" method="post" class="uam_login_form">';
    $loginForm .= '<label class="input_label" for="user_login">'.__('Username:', 'user-access-manager').'</label>';
    $loginForm .= '<input name="log" value="'.esc_html(stripslashes($userLogin), 1).'" class="input" id="user_login" type="text" />';
	$loginForm .= '<label class="input_label" for="user_pass">'.__('Password:', 'user-access-manager').'</label>';
    $loginForm .= '<input name="pwd" class="input" id="user_pass" type="password" />';
    $loginForm .= '<input name="rememberme" class="checkbox" id="rememberme" value="forever" type="checkbox" />';
    $loginForm .= '<label class="checkbox_label" for="rememberme">'.__('Remember me', 'user-access-manager').'</label>';
    $loginForm .= '<input class="button" type="submit" name="wp-submit" id="wp-submit" value="'.__('Login', 'user-access-manager').' &raquo;" />';
    $loginForm .= '<input type="hidden" name="redirect_to" value="'.$_SERVER['REQUEST_URI'].'" />';
    $loginForm .= '</form>';
                   */
                  
                  $loginUrl = get_bloginfo('wpurl').'/wp-login.php';
                  $loginUrl = apply_filters('uam_login_form_url', $loginUrl);
                  
                  ?>
                  <form action="<?php echo $loginUrl; ?>" method="post" class="uam_login_form">
                    <label id="fUser-label" class="text-input-label" for="fUser">Email</label>
                    <input class="text-input" type="text" id="fUser" name="log" autocomplete="off" />
                    <label id="fPass-label" class="text-input-label" for="fPass">Password</label>
                    <input class="text-input" type="password" id="fPass" name="pwd" autocomplete="off" />
                    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                    <input class="btn" type="submit" value="LOGIN" />
                  </form>
                </div>
                <div class="media-right">
                  <h2>Register</h2>
                  <p>
                    <a class="popup-request media-request" href="<?php echo Mage::getUrl("autelcorto/general/mediarequest");?>">Click here to register!</a><br />
                    <a class="popup-request password-request" href="<?php echo Mage::getUrl("autelcorto/general/forgotpassword");?>">Forgot your Media password?</a><br />                    
                  </p>
                </div>
                <div class="clear"></div>
              </div>
              <script type="text/javascript">
              
              Varien.watermakField = Class.create();
              Varien.watermakField.prototype = {
                  initialize : function(field, label){
                      this.field  = $(field);
                      this.label = $(label);

                      Event.observe(this.field, 'focus', this.focus.bind(this));
                      Event.observe(this.field, 'blur', this.blur.bind(this));
                      this.blur();
                  },

                  focus : function(event)
                  {
                    this.label.hide();
                  },

                  blur : function(event){
                    if(this.field.value=='')
                    {
                      this.label.show();
                    }
                  }
                }
                
                var email = new Varien.watermakField('fUser', 'fUser-label');
                var pass = new Varien.watermakField('fPass', 'fPass-label');
            
              </script>
              
              <?php endif; ?>

              
			</div><!-- #content -->
		</div><!-- #primary -->
		
<script>
	$j("a.popup-request").fancybox({
            autoScale   : true,
            padding     : "0",
            margin      : "0",
            showCloseButton : false
           });
</script>

<?php get_sidebar('reserved'); ?>
        
        <?php /*
        <div id="secondary" class="widget-area col-left sidebar" role="complementary">
          
          <div id="menu">
            <div class="container">
              
                <aside class="widget module">
					<h1 class="widget-title"><a href="#">Media home</a></h1>
				</aside>
                <aside class="widget module">
					<h1 class="widget-title"><a href="#">Press releases</a></h1>
				</aside>
                <aside class="widget module">
					<h1 class="widget-title"><a href="#">Collections</a></h1>
				</aside>
                <aside class="widget module">
					<h1 class="widget-title"><a href="#">Brand assets</a></h1>
				</aside>
              
            </div><!-- .container -->
          </div><!-- #menu -->
              
		</div><!-- #secondary .widget-area -->
        */ ?>
        
        <div class="clear"></div>
<?php get_footer(); ?>