/**
 * Gestione Menu
 */

var homeNavigator={};
if (typeof(totImgBkg)=="undefined") {
    var totImgBkg=4;
} else {
	if (totImgBkg==0) {
		totImgBkg=4
	}
}
if (typeof(pathImgBkg)=="undefined") {
    var pathImgBkg="/media/"
}
if (typeof(ofList)=="undefined") {
    var ofList = Array();
}

var ofPos = 0;
if (typeof(no_scroll)=="undefined") {
	var no_scroll=true;
}

if (typeof(isHomePage)=="undefined") {
	var isHomePage=false;
}

if (typeof(baseUrl)=="undefined") {
    var baseUrl = "";
}

var sizeTimer = null;

var isiPad = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);      


var imageHome = new Image();
var imageHomeLoaded = false;

var menuTimer = null;

(function($) {
    $.fn.setBackGroundPage = function() {
	var nr = 1; 
	if (totImgBkg != 1 && totImgBkg != 0) {
	    	nr=Math.floor(Math.random()*(totImgBkg+1));
    		if (nr==0) {nr=1;}
	}
        var imgPath=pathImgBkg+'layout/sfondi/catalog/img_'+nr.toString()+".jpg";
        if ($j.browser.msie && $j.browser.version <= 8) {
        	$j(".wrapper-background").css({"filter": "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+imgPath+"',sizingMethod='scale')"});
        } else {
	        $j(".wrapper-background").css('background-image','url('+imgPath+')');
	    }
    }
})(jQuery);

(function($) {
    
    $(document).ready(function() { 
        //Link asyncroni
        $('a[mps-type="async-link"]').on('click', function(evt) {
            evt.preventDefault();
            evt.stopPropagation();
            if ($(this).attr('href') != '' && $(this).attr('href') != '#') {
                $.fn.AsyncRedirect($(this).attr('href'));
            }
        });
    });
    
    $.fn.AsyncRedirect = function(url) {
        if (typeof(url) != 'undefined' && url != '') {
            $.fn.layer(true, {waiting: Translator.translate('waitingImage'), bindEsc:false})
            window.location = url;
        }
    }; 
    
    
    /**
     * Visualizza Nasconde il loading o il pannello di elaborazione
     */
    $.fn.layer = function(show, params) {
        var opts = $.extend({}, {exOnHide: function(){}, zInd:20000, waiting: "", bindEsc:true, handle:null}, params);                                
        var myHandle="layer-block";
        if (opts.handle!=null) {
        	myHandle=opts.handle;
        }
        var hideEle= function() {
                        $("#"+myHandle).fadeOut(100);
                        $("#"+myHandle).remove();
                        if (typeof($.fn.fancybox) != 'undefined')
                            $.fancybox.hideActivity();
                    };
        if (show){
            if ($("#"+myHandle).length<=0) {
                $("body").append($("<DIV>", {id: myHandle}).addClass("popup-layer")
                                        .css({"position":"fixed", "z-index": opts.zInd,
                          "top":"0px", "left":"0px", "background-color":"black",
                          "width":"100%", "height":"110%", "opacity": 0.2})
                    );
            }
            if (opts.waiting) {
                if (typeof($.fn.fancybox) != 'undefined')
                    $.fancybox.showActivity();
                else 
                    $('#'+myHandle).append($("<img>", {src: opts.waiting}).css({position: "absolute", margin: "50% auto"}));
            }
            $('#'+myHandle).show();
            if (opts.bindEsc){
                $('#'+myHandle).on("click", function (){
                        opts.exOnHide();
                        hideEle();
                        return false;
                    })
                $(window).on('keydown', function(evt) {
                    if (evt.keyCode === 27) {
                        $.fn.layer(false, {exOnHide: opts.exOnHide, bindEsc: true, handle: opts.handle});
                    }                
                    evt.stopPropagation();
                });
            }

        } else {
            opts.exOnHide();
            $("#"+myHandle).fadeOut(100);
            $("#"+myHandle).remove();
            if (opts.bindEsc) {
                $(window).off('keydown');
            if (typeof($.fn.fancybox) != 'undefined')
                            $.fancybox.hideActivity();
            }
        }
    }
})(jQuery);


/**
 * Manage Header Switcher
 * 
 */
(function ($, window, undefined) {  
    jQuery.fn.headerSwitcher = function(al) {        
    	var $this = $(this);   
        if (al==null) al ="";
    	$this.unbind("click");
    	$this.click(function() {
            var $popUp = $('[rel="'+$(this).attr("class")+'"]');
            if ($popUp.css("display") == "none") {
                var topOffset=30;//=$(this).parent("LI").offset().top+30;
                if ($(".header").css("position")=="absolute") {
                    topOffset=-1*( $popUp.outerHeight()+10);
                }
    		$.fn.layer(true, {zInd: (parseInt($popUp.css("z-index"))-10)||20001, 
                                  exOnHide: function (){$($popUp.selector).slideUp("fast");}});   
                switch (al) {
                    case "L":
                        $popUp.css('left', $(this).parent("LI").offset().left-parseInt($popUp.css("padding-left")));
                        break;
                    case "R":
                        $popUp.css('left', $(this).parent("LI").offset().left+$(this).outerWidth(true)-$popUp.outerWidth(true));
                        break;
                    default:
                        $popUp.css('left', $(this).parent("LI").offset().left+$(this).outerWidth(true)/2-$popUp.outerWidth(true)/2);
                        break;
                }

                $popUp.css('top',  $(this).position().top+topOffset);
                $popUp.css('position', 'absolute');                
                $popUp.slideDown('fast');
            } else {
                $popUp.slideUp("fast", $.fn.layer(false));    
            }
            return false;
        });
    	
        $('[rel="'+$this.attr("class")+'"] a.btn-remove').on("click", function() {
            $('[rel="'+$this.attr("class")+'"]').slideUp("fast", $.fn.layer(false));  
        })                 
    }
}) (jQuery, this);


(function($,window,undefined){
    jQuery.fn.crmMenu = function(current,params) {


        function CortoMenu(elem, category, p) {
            //Var
            var settings = $.extend({}, $.fn.crmMenu.defaults, p);                                
            var container = $(".container");

            function toggleMenu(myElem) {
                container.find("UL").each(function(idx, ul) {$(this).slideUp(settings.speed)});
                $('[sel="X"]').attr("sel","");
                $(myElem).attr("sel","X");
                $('UL [rel="'+$(myElem).attr("rel")+'"]').parent("UL").slideDown(settings.speed);                        
            }                

            if (category!=null && category!="") {
                toggleMenu($('UL [rel="'+category+'"]').parent("UL").parent(".module").children(".title").children("a"));
            } else {
                $(".title A").each(function(){
                    toggleMenu($(this));
                    return false;
                });
            }

            $(".title A").bind("click", function() {
                if ($(this).attr("href")+"#"!="#") {
                    window.location=$(this).attr("href");
                    return false;
                }
                var item = $(this).attr("rel");
                var selItem =$('[sel="X"]').attr("rel");
                if (typeof(selItem)=="undefined") {
                    selItem="";
                }
                if (item!=selItem && $('UL [rel="'+item+'"]').length>0) {
                    toggleMenu($(this));

                }
                return false;
            });                    

        }

        CortoMenu($(this), current, params);
        return this;
    }

    $.fn.crmMenu.defaults = {
            speed: "fast"
    }



})(jQuery,this);

/**
 * Gestione more-views
 *
 */

(function ($, window, undefined) {  
    jQuery.fn.moreViews = function() {

        var ulHeight = ($($(this).selector+">ul>li").outerHeight() + 8) * ($($(this).selector+" ul li").length/3).ceil();
        var mvHeight = $(this).height();
        var elem = $(this).selector;
        var mView = $(this);
        function MoreView() {
            
            function mViews() {

                $(elem+"").css("height", mvHeight +"px");
                $(elem+">ul").css("display","none");
                $(elem+">a").bind("click", function () {
                   var disp = $(elem+">ul").css("display");
                   showMoreViews((disp=="none"));                   
                });
                return false;
            }

            function showMoreViews(show) {
                if (show) {
                    $j(".product-detail-body").slideUp("fast");
                    $(elem+"").animate({height: mvHeight  + ulHeight+"px"}, "fast");
                    $(elem+">ul").slideDown("fast");
                } else {
                    $(elem+"").animate({height: mvHeight +"px"}, "fast");
                    $(elem+">ul").slideUp("fast");
                }
            }   
            
            
            $.extend(this, {
                ShowMoreViews: function (show) {
                    showMoreViews(show);
                }
            });
            
            mViews();
        }
                
        return this.each (function () {
            var myApi = $(this).data("mview");
            if (typeof(myApi)=="undefined") {
                myApi = new MoreView();
                $(this).data("mview", myApi);
            } 
        })
                      
    }
    
    
    
}) (jQuery, this);

/**
 * Inserisce un titolo custom per fancy box nel caso si utilizzi la pagina popup
 *  how to use
 * $j(".component").fancybox({
 *         onComplete: function () {
 *             $j.fn.fancyTitle("<?php echo $this->__('My title');?>");
 *         }  
 *     }); *
 */

(function ($, window, undefined) {  
    jQuery.fn.fancyTitle = function(title) {                
        if ($('.page-popup #title').length>0 && title != null) {
            $('.page-popup #title').text(title);
        }
    }
}) (jQuery, this);

(function ($, window, undefined) {  
    jQuery.fn.storeSwitcher = function(formLanguageClass) {        
        
        $(this).bind("click", function() {        
            var topOffset=20;
            if ($(".header").css("position")=="absolute") topOffset=-1*($(formLanguageClass).outerHeight()+20);
            if ($(formLanguageClass).css("display") == "none") {
                $(formLanguageClass).css('left', $(this).position().left-10);
                $(formLanguageClass).css('top', $(this).position().top + topOffset);
                $(formLanguageClass).css('position', 'absolute');
                $(formLanguageClass).slideDown('fast');
            } else {
                $(formLanguageClass).slideUp("fast");    
            }
            return false;
        });
        
        $(formLanguageClass+" .btn-remove").bind("click", function() {
            $(formLanguageClass).slideUp("fast");
            return false;
        })
        
        $(formLanguageClass+" a:not(.btn-remove)").bind("click", function() {
            var href = $(this).attr("href");
            if (href!="" || href!="#") window.location = href;
            return false;
        });
        
    }
}) (jQuery, this);

(function ($, window, undefined) {  
    jQuery.fn.searchForm = function(searchFormClass) {        
        
        $(this).bind("click", function() {            
            var topOffset=30;
            if ($(".header").css("position")=="absolute") topOffset=-1*($(searchFormClass).outerHeight()+10);
            if ($(searchFormClass).css("display") == "none") {
                $(searchFormClass).css('left', $(this).position().left+$(this).outerWidth(true)-$(searchFormClass).outerWidth(true));
                $(searchFormClass).css('top', $(this).position().top + topOffset);
                $(searchFormClass).css({'position': 'absolute', 'z-index': 1000});
                $(searchFormClass).slideDown('fast');
                $(".wrapper").bind("click", function(e) {
                    if (e.target.id!="search" && e.target.className!="btn_search") {
                        $(searchFormClass).slideUp("fast");
                        $(".wrapper").unbind("click");
                    }                   
                })
            } else {
                $(searchFormClass).slideUp("fast");    
            }
            return false;
        });        
    }
}) (jQuery, this);


(function ($, window, undefined) {  
    jQuery.fn.ToolTip = function(imgList) {        
        
        var myElem = $(this);
        
        function hideTT(elem) {
            $(".product-tooltip").each(function() {
                $(this).fadeOut("slow");
            })
            setTimeout(function () {showTT(elem)}, 7000);
        }
        
        function showTT(elem) {            
            var ii=0;
            $(".product-tooltip").remove();
            while (ii++<10) {
                var rnd = Math.floor(Math.random()*elem.find(".products-grid").find("LI").length+1);
                if (elem.find(".products-grid").find("LI")[rnd].classList[1]==null||elem.find(".products-grid").find("LI")[rnd].classList[1]!="img-catalog") {
                    elem.find(".products-grid").find("LI").each(function (idx) {
                        if (idx==rnd) {
                            $(this).children("A").append($("<DIV>",{"class": "product-tooltip"}).css({"position":"absolute",
                                                                                                      "top":"0", 
                                                                                                      "right":"0",
                                                                                                      "width":"110%",
                                                                                                      "display":"none"}
                                             ).append($("<img>").attr("src", imgList[Math.floor(Math.random()*2)]).css({"left":"100px", "background-color":"transparent"})));
                            $(".product-tooltip").fadeIn("slow");
                            ii=10   ;                            
                        }
                    });
                }
            }
            setTimeout(function () {hideTT(elem)}, 3000);
        }
        
        setTimeout(function () {showTT(myElem)}, 7000);
        
    }
}) (jQuery, this);
/**
 * Binding del trigger di resize
 */

function setImgData() {
	if (imageHomeLoaded) {
		//if (imageHome.height > $j('#img-home-a').outerHeight()) {
		//	$j('#img-home-a #img-home-img').css({'width': 'auto', 'height': '100%'});
		//} else {
			$j('#img-home-a #img-home-img').css({'width': '100%', 'height': 'auto'});
		//}	
		if ($j('#img-home-a #img-home-img').outerHeight() > 0) {
			var margin = ($j('#img-home-a').height() - $j('#img-home-a #img-home-img').outerHeight()) /2;
			if (margin > 0) {
				$j('#img-home-a #img-home-img').css({"margin-top":margin+"px", "visibility":"visible"});
			} else {
				$j('#img-home-a #img-home-img').css({"margin-top":"0px", "visibility":"visible"});
			}
		}else {
			$j('#img-home-a #img-home-img').css({"margin-top":"0px", "visibility":"visible"});
		}
	}
}


function adjustHeights() {
    if (sizeTimer != null) {
        clearInterval(sizeTimer);
    }
    var hH=$j('.header').outerHeight();
    var hF=$j('.footer_row bottom').outerHeight();
    var hW=$j(window).height();
    var h = hW-hH-hF;    
    if (h>0) {
        for (var i=0; i < $j('.mps-force-fill').length; i++) { 
            $j($j('.mps-force-fill')[i]).css({'height' : h+'px'});
        }
    }
    
    $j(window).trigger('scroll');
}
      

$j(window).bind("scroll", function() {
  
    if (isHomePage) {  
//        var dbg = $j('.footer_row.bottom').position().top;
//        dbg += "#";
//        dbg += $j(window).scrollTop();
//        dbg += "#" + $j(window).height();
//        dbg += "#" + $j(window).height();
//        dbg += "#" + $j('.footer-block-bottom').outerHeight();
//        
//        dbg += "#" + ref;
//        console.log(dbg);
//        $j('#dbg').val(dbg);
        
        var ref = $j(window).scrollTop() + $j(window).height() - $j('.footer-block-bottom').outerHeight();
        
        if ($j('.footer_row.bottom').offset().top <= ref) {
            $j('.footer-block-bottom').css({'position' : 'relative'});            
        } else {
            $j('.footer-block-bottom').css({'position' : 'fixed'});            
        }
    }
  
});

/* Inizializzione link di testa */
(function ($) {
    $.fn.initializePopUpMenu = function () {
        /* Gestione Carrello */    
        jQuery('.top-link-cart').headerSwitcher("R");
        /* Gestione Language */
        if (jQuery(".top-link-account").length>0) jQuery(".top-link-account").headerSwitcher("L");

        /* Popup Menu */
        $j('a.class-popuplogin').fancybox({autoScale   : true,
                                     showCloseButton : false,
                                     href        : baseUrl+"mini-login/",
                                     titleShow   : false});
    }
})(jQuery);


jQuery(window).resize(function() {
    if (sizeTimer != null) {
        clearInterval(sizeTimer);
    }
    
    sizeTimer = setInterval(function(){adjustHeights()},1000);

})
    

/* Getstione funzione a fine pagina */
jQuery(document).ready(function(){  

    if (isHomePage===true) {        
        adjustHeights();
    }
    //jQuery(window).trigger('resize');
    
    /* Link Arcobaleno */
    jQuery.fn.RainbowLink();
    
    jQuery.fn.initializePopUpMenu();
        
    if ($j('#home-navigator').length>0) {
        $j('#home-navigator').bind("click", function () {
            if ($j('#home-navigator').attr("rel").toUpperCase()=="UP") {
                $j('html,body').animate({"scrollTop":"0px"}, "slow", null, function() {
                    $j('#home-navigator').attr("rel","donw")
                    $j('#home-navigator>img').attr("src",homeNavigator.down);
                });
            } else {
                h=$j(window).height();
                h=h*(Math.ceil(($j(window).scrollTop()/h))+1);
                $j('html,body').animate({"scrollTop":h+"px"}, "slow", null, function() {
                    $j('#home-navigator').attr("rel","up")
                    $j('#home-navigator>img').attr("src",homeNavigator.up);
                });
                //if (jwplayer('mediaplayer').getState()!="PAUSED" && jwplayer('mediaplayer').getState()!="IDLE") {
                //   jwplayer('mediaplayer').pause(true);
                //}
            }
            return false;
        });
    }
       
	// Social per WP
	jQuery('.social-link > div').each(function() {
            if (jQuery(this).children('.social-set').length>0) {
                jQuery(this).bind('mouseover', function() {   
                    jQuery('.social-real').css({display:'none'});
                    jQuery(this).children('.social-real').css({"display": "block"});
                });
            }
        }).bind('mouseleave', function() {                    
            jQuery('.social-real').css({display:'none'});
        });
        
        if ($j('#js-news').length > 0) {$j('#js-news').liScroll()};
        
        if ($j('.mps-aync-img-loading').length > 0) {
            $j('.mps-aync-img-loading').each(function() {
                var elem = $j(this);
                var image = new Image();
                
                image.src = elem.attr('mps-aync-img-loading');
                image.onload = function() {
                    elem.attr({'src': image.src});                    
                };
                image.onerror = function() {
                    console.log("ERRORE");
                    elem.attr({'src': image.src});                    
                }; 
            });
        }
        

        if (baseUrl != "")
            jQuery(("a[href^='mailto:']")).each( function() {
                var info = {};
                if ($j(this).attr('mailinfo'))
                        eval("info = {" + $j(this).attr('mailinfo') + "};");
                var url = baseUrl + "autelcorto/general/mailto/address/" + encodeURIComponent($j(this).attr('href')) + "/";
                if (info.object) {
                    url += "object/" + encodeURIComponent(info.object) + "/";
                }
                $j(this).fancybox({
                        autoScale   : true,
                        padding     : "0",
                        margin      : "0",
                        href        : url,
                        showCloseButton : false ,
                        onComplete: function () {
                            if (info.title) {
                                $j.fn.fancyTitle(info.title);
                            }
                        }
                    });
             }); 

}); 

(function($) {
    $.fn.deleteError = function(message) {
        alert (message);
        return false;
    }       
    $.fn.deleteSuccess = function(result, message) {
        var data=$.parseJSON(result);
        if (data.result.toLowerCase()=="ko" || data.id==0){
            $.fn.deleteError(message);
        } else {
            $('[rel="'+data.removeItem+'"]').css({"min-height": "0px"});
            $('[rel="'+data.removeItem+'"]').slideUp("slow", function (){
                var actOffset=$(".light").offset();
                $(".light").replaceWith(data.block)
                $(".light").css({"display":"block", "visibility":"visible"}).offset(actOffset);
                jQuery('.links').each(function() {
                    if (jQuery(this).attr("class").split(" ").length==1) {
                            jQuery(this).replaceWith(data.links);
                            return false;
                    }                            		
                });
                $.fn.initializeLightCart(false);
                $.fn.initializePopUpMenu();
            })                                    
            //$(".top-link-cart").trigger("click");
        }
    }
    
    $.fn.onHideCart = function () {
        $(".light .coupon-box").fadeOut("fast", function () {
                $(".light .coupon-view").fadeIn("fast", function () {
                    if ($('.light #coupon_code').attr("rel")!="") {
                        $('.light #coupon_code').val($('.light #coupon_code').attr("rel"));
                        $('.light #coupon_code').attr("rel", "");
                    }
                });
            });                    
    }
    
    $.fn.CouponManager = function (couponUrl, remove) {
        if (typeof(remove)=="undefined") {remove=false;}
        $.fn.layer(true,{waiting: Translator.translate('waitingImage'), bindEsc:false, "handle":"apply-coupon" });        
        $.ajax({
            data : {'code' : $('.light #coupon_code').val(), 'remove': (remove)?1:0 },
            type : 'POST',
            url : couponUrl,
            error : function () {
                alert (Translator.translate('lightCartCouonErrorMessage'));
                return false;                       
            },
            success: function (res) {
                var data=$.parseJSON(res);
                if (data.status.toUpperCase()!='NO_ACTION') {
                     if (data.status.toUpperCase()=="ERROR" || data.status.toUpperCase()=="NO_APPLY") {
                         alert(data.message);
                     } else {
                        var actOffset=$(".light").offset();
                        $(".light").replaceWith(data.block)
                        $(".light").css({"display":"block", "visibility":"visible"}).offset(actOffset);
                        $.fn.initializeLightCart(false);
                        $.fn.initializePopUpMenu();
                     }
                 }
            }, 
            complete: function () {
                $.fn.layer(false,{"handle":"apply-coupon"});
            }
        });
    }
    
    $.fn.initializeLightCart = function (setInvisible) {
        
        $(window).keydown(function(evt) {
            if (evt.keyCode==27) {
                $.fn.onHideCart();
            }
        });
        
        $('#layer-block').click(function() {
            $.fn.onHideCart();
        });
        
        if ($(".jsp-light-cart ol li").length>3) {
            $(".jsp-light-cart").addClass("jsp-enable");
            $(".jsp-light-cart").jScrollPane();
        }
        if (setInvisible!==false) {$(".light.block-cart").css({"display":"none", "visibility": "visible"})};
        $(".product-remove a").unbind("click");
        $(".product-remove a").click(function(evt) {
            var confirmMessage=Translator.translate('lightCartConfirmMessage');
            var errorMessage=Translator.translate('lightCartErrorMessage');
            if (confirm(confirmMessage)) {
                $.fn.layer(true,{waiting: Translator.translate('waitingImage'), bindEsc:false, "handle":"cart-remove", exOnHide: $.fn.onHideCart});
                var deleteUrl=jQuery(this).attr("href");
                $.ajax({
                    url : deleteUrl,
                    error : function () {
                        $.fn.deleteError(errorMessage);                        
                    },
                    success: function (res) {
                        $.fn.deleteSuccess(res, errorMessage);
                    }, 
                    complete: function () {
                        $.fn.layer(false,{"handle":"cart-remove"});
                    }
                });
            }
            evt.stopPropagation();
            return false;
        });
        $(".light .coupon-view").click(function (event) {           
           event.preventDefault();
           $(this).fadeOut("fast", function () {$(".light .coupon-box").fadeIn("fast");});                     
           $(".light #coupon_code").click(function() {
               if ($(this).attr("rel")=="") {
                   $(this).attr("rel", $(this).val());
                   $(this).val("");                   
               } 
           }).keydown(function(evt) {
               switch (evt.keyCode) {
                   case 27:                     
                      $.fn.onHideCart();
                      evt.stopPropagation();
                      break;
                   case 13:
                       $(".light #btn_accpet").trigger("click");
                       evt.stopPropagation();
                       break;
                   default:
                       $(".light #coupon_code").removeClass("required-entry").removeClass("validation-failed");
                       break;
               }                
           })
           $(".light #btn_accpet").click(function(evt) {
               if ($(".light #coupon_code").attr("rel")=="" || $(".light #coupon_code").val() == "") {
                   $(".light #coupon_code").addClass("required-entry validation-failed");
               } else {
                   $.fn.CouponManager($('.light .coupon-view').attr("href"));
                   evt.stopPropagation();
               }
           })
        });
        $(".light .coupon-remove").click(function (event) {           
            event.preventDefault();
            $.fn.CouponManager($(this).attr("href"), true);
        });        
    }
})(jQuery);

(function ($, window, undefined) {  
    jQuery.fn.RainbowLink = function(cssElem, executeRainbow, isMenu)
    {      
    
	    var RLrate = 15;  // Increase amount(The degree of the transmutation)
	    var RLact = 0;    // Flag during the action
	    var RLelmH = 0;   // Hue
	    var RLelmS = 128; // Saturation
            var RLelmV = 255; // Value
	    var RLclrOrg;     // A color before the change
	    var RLTimerID;    // Timer ID  
    	var elem;
        if (typeof(cssElem)=="undefined" || cssElem=="") {
            cssElem = "color";
        }
        
        if (executeRainbow) {
            RLact = 1;
            elem = $(this);
            RLclrOrg = elem.css(cssElem);
            return (setInterval(function(){ChangeColor();},100));
        } else {
            $('.rainbow-link').bind('mouseenter', function() 
                {
                    if (RLact==0)
                    {
                                    RLact = 1;
                                    elem = $(this);
                                    RLclrOrg = elem.css(cssElem);                                    
                                    RLTimerID = setInterval(function(){ChangeColor();},100);
                                    if (elem.hasClass('header-menu')) {
                                        $('#menu-header-list li.is-menu-select a').css(cssElem,RLclrOrg);
                                        clearInterval(menuTimer); 
                                    }
                    }
                });    

                $('.rainbow-link').bind('mouseleave', function() 
                {
                    $(this).css(cssElem,RLclrOrg);
                    clearInterval(RLTimerID);
                    RLact = 0;
                    if (elem.hasClass('header-menu')) {
                        menuTimer = $('#menu-header-list li.is-menu-select a').RainbowLink("", true, true );
                    }
            });
        }
        
        function ChangeColor()
        {
        	elem.css(cssElem,RLMakeColor());
        }
		
		function RLMakeColor()
		{
		    // Don't you think Color Gamut to look like Rainbow?
		
		    // HSVtoRGB
		    if (RLelmS == 0) {
		        elmR = RLelmV;elmG = RLelmV;elmB = RLelmV;
		    }
		    else {
		        t1 = RLelmV;
		        t2 = (255 - RLelmS) * RLelmV / 255;
		        t3 = RLelmH % 60;
		        t3 = (t1 - t2) * t3 / 60;
		
		        if (RLelmH < 60) {
		            elmR = t1;elmB = t2;elmG = t2 + t3;
		        }
		        else if (RLelmH < 120) {
		            elmG = t1;elmB = t2;elmR = t1 - t3;
		        }
		        else if (RLelmH < 180) {
		            elmG = t1;elmR = t2;elmB = t2 + t3;
		        }
		        else if (RLelmH < 240) {
		            elmB = t1;elmR = t2;elmG = t1 - t3;
		        }
		        else if (RLelmH < 300) {
		            elmB = t1;elmG = t2;elmR = t2 + t3;
		        }
		        else if (RLelmH < 360) {
		            elmR = t1;elmG = t2;elmB = t1 - t3;
		        }
		        else {
		            elmR = 0;elmG = 0;elmB = 0;
		        }
		    }
		
		    elmR = Math.floor(elmR).toString(16);
		    elmG = Math.floor(elmG).toString(16);
		    elmB = Math.floor(elmB).toString(16);
		    if (elmR.length == 1)    elmR = "0" + elmR;
		    if (elmG.length == 1)    elmG = "0" + elmG;
		    if (elmB.length == 1)    elmB = "0" + elmB;
		
		    RLelmH = RLelmH + RLrate;
		    if (RLelmH >= 360)
		        RLelmH = 0;
		
		    return '#' + elmR + elmG + elmB;
		}
        
    }
}) (jQuery, this);

/* Getstione funzione a fine pagina */
jQuery(window).load(function(){  
    $j(window).trigger('scroll');
});
