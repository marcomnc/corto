if (typeof(Social) == 'undefined') {
    var Social = {};
}
/**
 *  Classe per fb connect 
 *  Creare il seguente tag per l'incclusione degli scrip di facebook
 *  <div id="fb-root"></div>
 */
Social.FBConnect = Class.create();
Social.FBConnect.prototype = {
    initialize: function (appId,IdName,reqUrl) {
        this.imgLoaderUrl = null;
        this.requestUrl = reqUrl;
        this.elementId = IdName;
        $(''+this.elementId).observe('click', this.fbLogin.bind(this));
        window.fbAsyncInit = function() {        
            FB.init({appId: ''+appId, status: true, cookie: true,
                        xfbml: true, oauth : true});
            };    
        var e = document.createElement('script');
        e.type = 'text/javascript';
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js'
        e.async = true;
        document.getElementById('fb-root').appendChild(e);      
    },       
    fbLogin: function (event) {
        var eId = this.elementId;
        var iLoader = this.imgLoaderUrl;
        var iOri = $(eId).src;
        var rUrl = this.requestUrl;
        FB.login(function(response) {
            if (iLoader != null) {
                $(eId).src = ''+iLoader;
            }
            if (response.authResponse) {
                FB.api('/me', function(response) {
                    new Ajax.Request(rUrl, {
                        method: 'post',
                        asynchronous: false, 
                        parameters: "",
                        onComplete: function(transport) {
                            var result = transport.responseText.evalJSON()
                            if (result[0]) {
                                window.location = result[1];
                            } else {$(eId).src = ''+iOri;} 
                        }
                    })
             })
          } else {$(eId).src = ''+iOri;} 
        }, {scope: 'email,user_birthday'});
    },
    setLoaderUrl: function (imgUrl) {
        this.imgLoaderUrl = imgUrl;
    }          
}

function fbs_click(urlToShare, pictureToShare, nameToShare, titleToShare, contentToShare) {
    var href="http://www.facebook.com/dialog/feed?" +
             //"app_id=433454403372693&amp;"+
             "app_id=267246793303355&amp;"+
             "link="+ urlToShare +"&amp;" + 
             "picture=" + pictureToShare + "&amp;" +
             "name=" + nameToShare + "&amp;" + 
             "caption=" + titleToShare + "&amp;" +
             "description=" + contentToShare + "&amp;" +
             "redirect_uri=http://www.corto.com/en/window-close";
     
    window.open(href,'fb_sharer','toolbar=0,status=0,width=626,height=436');
    return false;
}

function tws_click(urlToShare, titleToShare) {
    window.open('http://twitter.com/intent/tweet?url='+ urlToShare + '&hashtags=cortomoltedo&text=' + titleToShare,'tw_sharer','toolbar=0,status=0,width=626,height=436');
}

function pin_click(urlToShare, titleToShare, urlImg){
    window.open('http://pinterest.com/pin/create/button/?url='+ urlToShare +'&media='+ urlImg +'&description='+ titleToShare +'','Pin_sharer','toolbar=0,status=0,width=626,height=436');
}


