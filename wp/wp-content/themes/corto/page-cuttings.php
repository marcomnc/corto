<?php
/**
 * Template Name: Press Cuttings landing page
 * Description: Cuttings list with pagination
 *
 * @package Corto
 * @since Corto 0.1
 */

get_header(); ?>

<div id="primary" class="col-main">
    <div id="content" role="main">

<?php
  $wp_query = null;
  $wp_query = new WP_Query();
  get_posts(array());

  $q = array(
      'post_type'=>'press',
      'paged'=>$paged,
      'showposts'=>'24'
  );
  $wp_query->query($q); 
  
?>

      
    </div><!-- #content -->
</div><!-- #primary -->

    <header class="page-header">
        <div class="breadcrumbs">
            <ul>
                <li><a href="<?php echo get_site_url(); ?>"><?php echo Mage::Helper('autelcorto')->__('News & Events');?></a><span>|</span></li>
                <li class="last">Press cuttings</li>
            </ul>                
        </div>
    </header><!-- .container -->

  
</div><!-- #secondary .widget-area -->

 <?php corto_content_nav( 'nav-above' ); ?>

<div class="clear"></div>
<?php /* Chiusura alternativa del layout fuori dal footer */ ?>
      </div><!-- .main -->
    </div><!-- .main-container -->
  </div><!-- .page -->
</div><!-- #main -->
<?php /* FINE Chiusura alternativa del layout fuori dal footer */ ?>

<div class="cuttings-wrapper">
  <div class="cuttings">
    <?php if ( ! have_posts() ) : ?>
    <div class="cuttings-inner">
      <span>Oops, that's bad. No results.</span>
    </div><!-- #post-0 -->
    <?php endif; ?>

    <?php 
      if (have_posts()) : 
      $counter = 0;
    ?>
    <div class="cuttings-inner">
    <script type="text/javascript">
      var galleries = new Array();
      var files = new Array();
    </script>
    <?php while (have_posts()) : the_post(); $counter++; ?>

      <article class="cuttings-item">
        <a href="#" class="cuttings-item-link" id="item<?php echo $counter; ?>"><img src="<?php echo get_field('thumbnail') ?>" title="<?php the_title(); ?>" /></a>
        <script type="text/javascript">
          galleries['item<?php echo $counter;  ?>'] = new Array();
          <?php if(get_field('gallery')): ?>
            <?php while(the_repeater_field('gallery')): ?>
              galleries['item<?php echo $counter;  ?>'].push('<?php the_sub_field('gallery_image'); ?>');
            <?php endwhile; ?>
          <?php endif; ?>
            files['item<?php echo $counter;  ?>'] = '<?php echo get_field('pdf') ?>';
        </script>
      </article>

    <?php endwhile; ?>
      <div class="clear"></div>
    </div><!-- .cuttings-inner -->
    
    <script type="text/javascript">
    
    var currentItemId = "";
    var currentIndex = 0;
    
    /**
    * Dialog dettaglios
    *
    */
    (function ($, window, undefined) {
      jQuery.fn.cuttingDetail = function() {
        
        $('.cuttings-item-link').bind('click', function() {
          
          currentItemId = this.id;
          currentIndex = 0;
          var pWidth = jQuery(window).width();
          var pHeight = jQuery(window).height();
          var eWidth = 375;
          var eHeight = 510;
          
          var topOffset = $(window).scrollTop();
          
          var top = '0px'; 
          if (pHeight > eHeight)
            top = parseInt((pHeight / 2) - (eHeight / 2)) + topOffset + 'px';
          var left = parseInt((pWidth / 2) - (eWidth / 2)) + 'px';
                    
          $('#cuttings-dialog').css('top', top);
          $('#cuttings-dialog').css('left', left);
          setGalleryItem(currentItemId, currentIndex);
          $.fn.layer(true, {zInd: (parseInt($('#cuttings-dialog').css("z-index"))-10)||20001, 
                                  exOnHide: function (){$('#cuttings-close').trigger("click");}});   
          $('#cuttings-dialog').fadeIn('slow');
          $('#cuttings-download-link').attr('href', files[currentItemId]);
          return false;
        });
        
        $('#cuttings-close').bind("click", function(){
        
          $('#cuttings-dialog').fadeOut('fast', $.fn.layer(false));
          unsetGalleryItem();
          $('#cuttings-download-link').attr('href', '#');
          return false;
        });
        
        $('#prev-nav-arrow').bind('click', function()
        {
          if (currentIndex > 0)
          {
            currentIndex--;
            setGalleryItem(currentItemId, currentIndex);
            
          }
          return false;
        });
        
        //Ribindo o scroll perch� lo uso solo in home page
        var lastScroll =  $(window).scrollTop();
	    $(window).scroll(function(e) {
	        var newScroll =  $(window).scrollTop();
	        if ($('#cuttings-dialog').css("display") != "none") {
	        	var delta = lastScroll - newScroll;
	        	var curOff = $('#cuttings-dialog').offset();
	        	$('#cuttings-dialog').css({"top": (curOff.top-delta)+"px", "left": curOff.left+"px"});	        	
	        }
	        lastScroll = newScroll;
	    });
        
        $('#next-nav-arrow').bind('click', function()
        {
          if (currentIndex < galleries[currentItemId].length)
          {
            currentIndex++;
            setGalleryItem(currentItemId, currentIndex); 
            
          }
          return false;
        });
        
        
      }
    }) (jQuery, this);
    
    
    jQuery(document).ready(function(){
      jQuery.fn.cuttingDetail();
    }); 
    
    function setGalleryItem(itemId, index)
    {
      var img = galleries[itemId][index];
      jQuery('#cuttings-gallery-item-img').attr('src', img);
      
      if (currentIndex <= 0)
      {
        jQuery('#prev-nav-arrow').css('display','none');
      }
      else
      {
        jQuery('#prev-nav-arrow').css('display','block');
      }
      
      if (currentIndex >= (galleries[itemId].length)-1)
      {
        jQuery('#next-nav-arrow').css('display','none');
      }
      else
      {
        jQuery('#next-nav-arrow').css('display','block');
      }
      
    }
    
    function unsetGalleryItem(itemId, index)
    {
      jQuery('#cuttings-gallery-item-img').attr('src', '');
    }
  
    </script>
    

    
    
    <div id="cuttings-dialog" class="cuttings-dialog" style="display: none;">
      
      <a href="#" id="cuttings-close" class="btn-remove"></a>
      <div class="clear"></div>
      <div class="cuttings-gallery-item">
        <img id="cuttings-gallery-item-img" src="" />
        <a class="nav-arrow-prev" href="#" id="prev-nav-arrow"></a>
        <a class="nav-arrow-next" href="#" id="next-nav-arrow"></a>
        <a class="cuttings-download-link" href="#" id="cuttings-download-link" target="_blank">DOWNLOAD</a>
      </div>
      
    </div>
    
    <?php endif; ?>    
  </div><!-- .cuttings-inner -->
  <div class ="page nav-above-bottom">
    <?php corto_content_nav( 'nav-above' ); ?>
  </div>

<?php /* Chiusura alternativa del layout fuori dal footer */ ?>
<?php /* FINE Chiusura alternativa del layout fuori dal footer */ ?></div><!-- .cuttings-wrapper -->

<?php get_footer('cuttings'); ?>
