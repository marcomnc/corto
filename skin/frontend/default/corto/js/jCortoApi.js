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

var isiPad = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);      


var imageHome = new Image();
var imageHomeLoaded = false;


var nH=0;
var hH=0;


function encode64(input) {

     var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;
     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);
        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;
        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }
        output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) + keyStr.charAt(enc3) + keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);
     return output;
  }


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
                        $("#"+myHandle).remove();}
        if (show){
            if ($("#"+myHandle).length<=0) {
                $("body").append($("<DIV>", {id: myHandle}).addClass("popup-layer")
                                        .css({"position":"fixed", "z-index": opts.zInd,
                          "top":"0px", "left":"0px", "background-color":"black",
                          "width":"100%", "height":"110%", "opacity": 0.2})
                    );
            }
            if (opts.waiting!="") {
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
    $.fn.selectCountry = function (params) {
        var settings = $.extend({}, {'url': '', 'countryCode': '', "home": "0", "urlref" : '' }, params);
        if (settings.url != "") {
            var url = settings.url;
            if (settings.countryCode != "") {
                url+='countryCode/'+settings.countryCode+'/';
            }
            url+='home/'+settings.home+'/urlref/'+settings.urlref+'/'
            $j.fancybox({autoScale   : true,
                         showCloseButton : false,
                         href        : url,
                         modal       : true,
                         titleShow   : false});
        }
    }
})(jQuery, this);

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

jQuery(window).resize(function() {    
    
    if (isHomePage===true) {        
        adjustHeights();
    }
    var w=jQuery(window).height();
    var d=jQuery(document).height();
    var wh=jQuery(".page").height();
    var paddingTop=parseInt(jQuery(".wrapper-page").css("paddingTop"));
    var paddingBottom=parseInt(jQuery(".wrapper-page").css("paddingBottom"));
    var hv=d+(w-d)-jQuery(".header-container").height()-jQuery(".footer-container").height()-paddingBottom-paddingTop;    
    if (hv>0 && wh<hv && typeof(checkout)=="undefined") {jQuery(".wrapper-page").css({"height":hv+"px"});}
    
    //$j("#my").val("Width:" + jQuery(window).width()+"\nright width:"+$j('.right-nav').outerWidth(true));
    
    if (jQuery(window).width()<=1018) {
        if (jQuery(".right-nav").width()!=0) {
            jQuery(".right-nav").css({"width":"0px", "padding": "0px"});
        }
    } else {
        if (jQuery(".right-nav").width()==0) {
            jQuery(".right-nav").css({"width":"58px", "padding": "5px"});
        }
    }
    //$j("#my").val($j("#my").val()+"\n-----\nWidth:" + jQuery(window).width()+"\nright width:"+$j('.right-nav').outerWidth(true));
    if (w>=jQuery(".right-nav").height()) {
        var rnp=w/2-jQuery(".right-nav").outerHeight(true)/2;        
        rnp=Math.round(rnp);
        jQuery(".right-nav").css({"top": (rnp+$j('.home-bg-navigator').outerHeight(true))+"px"});
        jQuery(".home-bg-navigator").css({"top": rnp+"px"});
    }
});

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
    //if (nH==0) nH=$j('#news-home').outerHeight()+$j('.header').outerHeight();
    if (nH==0) nH=$j('#news-home').outerHeight();
    if (hH==0) hH=$j('.header').outerHeight();
    $j('.header-container').height($j(window).height());
    $j('.header-container').width($j(window).width()); 
    $j('#img-home-a').height($j('.header-container').height()-nH);
    $j('#img-home-a').css({'padding-top':$j('#news-home').outerHeight()+'px'});

	if ($j(imageHome).attr('src') != "" && typeof($j(imageHome).attr('src')) != 'undefined') {
		setImgData();
	} else {
		$j(imageHome).attr({'src' : $j('#img-home-a #img-home-img').attr('src')}).load(function() {
			imageHomeLoaded = true;
			//i think the page is ok			
			setImgData();
		});	
	}		
    

    $j('.home-slider-container').height($j(window).height()-$j('.header').height()-$j('.footer').outerHeight(true)+1);
    //$j('.footer-container').width($j(window).width()); 
    var w_p_s=($j('.home-slider-container').height()-$j(".background").height())/2;
    if (w_p_s>0) {
		$j(".shop-footer").css({"padding-top": w_p_s+"px"});
	}     
    $j(window).trigger("scroll");
    $j(".outfit-container li").css({"background-size": "auto "+$j(window).height()+"px", "height": $j(window).height()+"px"})
}

function postCreateOf (idx, liEle, class2add, last) {
    //console.log("Carico: " + liEle);
    //console.log("Conto: "+ $j(".outfit-container li").length);
    $j(".outfit-container").append($j("<li>", {"id": "outfit"+idx, "class":"outfit"})
                                    .css({"display": "none", "background-color": "trasparent", 
                                           "background-image": "url('"+liEle.src+"')",
                                           "background-repeat": "no-repeat",  
                                           "background-attachment": (!isiPad)?"fixed":"none", 
                                           "background-position": "center 60px",
                                           "background-size": "auto "+$j(window).height()+"px", 
                                           "-moz-background-size": "auto "+$j(window).height()+"px", 
                                           "-webkit-background-size": "auto "+$j(window).height()+"px", 
                                           "height": $j(window).height()+"px",
				           "cursor" : (liEle.url != "")?"pointer":"default"})
				    .attr('rel', liEle.url)
                                    .addClass(class2add)
                                    .append("<div class=\"slider-divider\"><div></div></div>"));    

   if (last) {
        $j.fn.layer(false, { bindEsc:false});
        $j(".outfit-container li").fadeOut();                
        $j(".outfit-container li.remove").remove()
        $j(".outfit-container li").fadeIn(); 
        if ($j(window).scrollTop() < 100) {
        	var pl = ($j("#img-home-a").outerWidth()/2 - $j("#scroll-down").outerWidth()/2);
        	$j("#scroll-down").css({'position':'absolute','bottom' : '60px', 'left': pl+'px', 'z-index': 10, 'cursor':'pointer' });
        	$j("#scroll-down").fadeIn("fast");
        	$j("#scroll-down").click(function() {
        			$j('#home-navigator').trigger("click");
        			$j(this).fadeOut("fast");
        		});
       		window.setTimeout(function(){
       				$j("#scroll-down").fadeOut("slow");
       			},5000);
        }
        //$j("body").css({'overflow-y' : 'scroll'});   
        no_scroll=false; 
        adjustHeights();
    }
 }
function createOf(imgList) {
                 
    no_scroll=true;
    if (imgList.length>0) {    	
    	if (imgList.length==1) {
    		jQuery(".home-bg-navigator").css({"display": "none"});
    	}
        if ($j(".outfit-container li").length>0) {
            $j(".outfit-container li").addClass("remove");            
        }        
        imgList.each(function (liEle, idx) {
            var ac = "";
            if (idx==0) ac="first-outfit";
            if (idx==(imgList.length-1)) ac="last-outfit";
            //console.log("precarico: " + liEle + " len " + $j(".outfit-container").length);
            var myImage=new Image();
            $j(myImage).attr('src', liEle.src).load(function() {postCreateOf(idx,liEle,ac,idx==(imgList.length-1))});
            
            if (idx==(imgList.length-1)) {
//                $j(".outfit-container li").fadeOut();                
//                $j(".outfit-container li.remove").remove()
//                $j(".outfit-container li").fadeIn();    
//                no_scroll=false;
            }
        });    
	$j(document).on('click', '.outfit', function () {
			var url=$j(this).attr('rel')+'';
			if ( url != '') {
				window.location =url;
			}
		});
    }    
}

function readyHomePage(idx) {
    $j("body").css({"background-color":"#FFF"})  
    //$j('.header-container').css({"position":"relative", "background-color":"#000"});
    $j('.footer-container').css({"background-color":"#000"});
    //$j(".header").css({"position":"absolute", "bottom":"0","width":"100%"});        
    idx=(idx==null)?0:idx;
    createOf(ofList[ofPos]);
    
    adjustHeights();
}
      

$j(window).bind("scroll", function() {
  if (!no_scroll) {            
    if (($j(window).scrollTop()-$j('.header-container').height())>-80) {
        $j('.header').removeAttr("style").css({"position":"fixed", "top":0, "left":0, "z-index":999, "width":"100%"});
        if (isHomePage===true) {
            $j('#home-navigator').attr("rel","up").css({"display":"block"});
            $j('#home-navigator>img').attr("src",homeNavigator.up);
            jQuery(".home-bg-navigator").removeClass("invisible").addClass("visible");
        }        
    } else {
        $j('.header').removeAttr("style").css({"position":"absolute", "bottom":0, "left":0, "width":"100%"});        
        if (isHomePage===true) {
            $j('#home-navigator').attr("rel","down")
            $j('#home-navigator>img').attr("src",homeNavigator.down);            
            if (($j(window).scrollTop()-$j('.header-container').height())>-1*($j('.header-container').height()/2)) {
                $j('#home-navigator').attr("rel","up").css({"display":"block"});
                $j('#home-navigator>img').attr("src",homeNavigator.up);
                jQuery(".home-bg-navigator").removeClass("invisible").addClass("visible");
                //if (jwplayer('mediaplayer').getState()!="PAUSED" && jwplayer('mediaplayer').getState()!="IDLE") {
                //   jwplayer('mediaplayer').pause(true);
                //}
            } else {                
                $j('#home-navigator').css({"display":"block"});
                jQuery(".home-bg-navigator").removeClass("visible").addClass("invisible");
            }
        }
    }
    
    if (($j(window).scrollTop()-$j('.header-container').height())>0) {
        $j(".outfit-container li:first-child").css({"background-position": "center 60px"});
    } else {
    	if (!isiPad) {
        	$j(".outfit-container li:first-child").css({"background-position": "center "+(60+$j('.header-container').height()-$j(window).scrollTop())+"px"});
        } else {
        	$j(".outfit-container li:first-child").css({"background-position": "center center"});
        }
    } 
    if (isHomePage===true) {
        var diff=$j(document).height()-$j(window).scrollTop();
        var pg=parseInt($j('.slider-logo').css("top"),0)+$j('.slider-logo').height()+$j('.footer-container').outerHeight(true);        
        if ((diff-pg)<0) {
            $j('.slider-logo>div').css({"overflow":"hidden", "height":parseInt($j('.slider-logo').height(),0)+(diff-pg)+"px"});
            if (-(diff-pg)>=$j('.slider-logo').height()){
                $j('.slider-logo>div').css({"overflow":"hidden", "height": "0px"});
                jQuery(".home-bg-navigator").removeClass("visible").addClass("invisible");
            }            
        } else {
            $j('.slider-logo>div').css({"overflow":"hidden", "height":$j('.slider-logo').height()+"px"})
        }
/*
        if ($j(window).scrollTop()>0 && jwplayer('mediaplayer').getState()!="PAUSED" && jwplayer('mediaplayer').getState()!="IDLE")
            jwplayer('mediaplayer').pause(true);
        if ($j(window).scrollTop()==0 && (jwplayer('mediaplayer').getState()=="PAUSED" || jwplayer('mediaplayer').getState()=="IDLE"))
            jwplayer('mediaplayer').play(true);*/
      }
  }
});

/* Inizializzione link di testa */
(function ($) {
    $.fn.initializePopUpMenu = function () {
        /* Gestione Carrello */    
        jQuery('.top-link-cart').headerSwitcher();
        /* Gestione Language */
        if (jQuery(".top-link-account").length>0) jQuery(".top-link-account").headerSwitcher("L");
        /* GEstione ricerca */
        if (jQuery(".top-link-search").length>0) jQuery(".top-link-search").headerSwitcher("R");        
        /* Popup Menu */
        $j('a.class-popuplogin').fancybox({autoScale   : true,
                                     showCloseButton : false,
                                     href        : baseUrl+"mini-login/",
                                     titleShow   : false});
    }
})(jQuery);

/* Getstione funzione a fine pagina */
jQuery(document).ready(function(){  

    if (isHomePage===true) {        
        homeNavigator=jQuery.parseJSON(jQuery('#home-navigator>img').attr("rel").replace(/\\\'/g, "\""));
        readyHomePage();
    }
    jQuery(window).trigger('resize');
    
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
    
    if ($j(".home-bg-navigator").length>0) {
    	if (ofList.length<=1) {
    		$j(".home-bg-navigator").css({ "display":"none" });
    	}
        $j(".home-bg-navigator a").bind("click", function () {
            if($j(this).attr("class").toUpperCase()=="RIGHT") {
                if (ofPos>=(ofList.length-1)) {
                    ofPos=0;
                } else {ofPos++}
            } else {
                if (ofPos==0) {
                    ofPos=ofList.length-1;
                } else {ofPos--}
            }
            $j.fn.layer(true, {waiting: "../images/waiting.gif", bindEsc:false});
            createOf(ofList[ofPos]);                        
            return false;
        })
    }

    if ($j(".product-detail").length>0) {
       /* Lunghezza della barra laterale del prodotto per evitare che scretcha quando implodo esplodo view e detail
       if ($j(".product-shop").length>0) {
           var h=$j(".product-shop").height()+0;
           if (h>0) $j(".product-shop").css({"min-height": h+"px"});
       }*/
       $j(".product-detail").css({"cursor":"pointer"})
       $j(".product-detail").bind("click", function() {
            if ($j(".product-detail-body").css("display")!="none") {
                $j(".product-detail-body").slideUp("fast");
            } else {
            	$j(".more-views").data("mview").ShowMoreViews(false);
                $j(".product-detail-body").slideDown("fast");
            }
        })
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
    jQuery.fn.RainbowLink = function(cssElem, executeRainbow)
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
                    }
                });    

                $('.rainbow-link').bind('mouseleave', function() 
                {
                    $(this).css(cssElem,RLclrOrg);
                    clearInterval(RLTimerID);
                    RLact = 0;
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
    
})

      
      
