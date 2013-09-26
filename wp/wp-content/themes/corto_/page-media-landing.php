<?php
/**
 * Template Name: Media landing page
 * Description: Login / logout form for the reserved area
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

		<div id="primary" class="col-main">
			<div id="content" role="main">
              
              <header class="page-header">
                  
                  <div class="breadcrumbs">
                    <ul>
                      <li>Media access</li>
                    </ul>
                  </div>

              </header>
              
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
                  <form>
                    <label id="fUser-label" class="text-input-label" for="fUser">Email</label>
                    <input class="text-input" type="text" id="fUser" name="fUser" autocomplete="off" />
                    <label id="fPass-label" class="text-input-label" for="fUser">Password</label>
                    <input class="text-input" type="password" id="fPass" name="fPass" autocomplete="off" />
                    <input class="btn" type="submit" value="LOGIN" />
                  </form>
<?php

//$uam = new UserAccessManager();
//echo $uam->getLoginBarHtml();

?>
                </div>
                <div class="media-right">
                  <h2>Register</h2>
                  <p>
                    Not yet registered for Media access?<br />
                    Forgot your Media password?<br />
                    <a href="#">Click here</a>
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
              
			</div><!-- #content -->
		</div><!-- #primary -->

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