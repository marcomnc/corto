(function($,window,undefined){
    $.fn.selectCountry = function (params) {
        var settings = $.extend({}, {'url': '', 'countryCode': '', "home": "0", "urlref" : '', 'enableEsc': false }, params);
        if (settings.url != "") {
            var url = settings.url;
            if (settings.countryCode != "") {
                url+='countryCode/'+settings.countryCode+'/';
            }            
            url+='home/'+settings.home+'/urlref/'+settings.urlref+'/'
            $j.fancybox({autoScale   : true,
                         showCloseButton : false,
                         href        : url,
                         modal       : !settings.enableEsc,
                         titleShow   : false});
        }
    };
    
    $.fn.setCountryCookie = function(href) {
        $j.ajax({
            url     : href,
            success : function(response) {
                var data = $j.parseJSON(response);
                if (data.action.toString().toLowerCase() == 'redirect' && data.url != "") {
                    window.location = data.url;
                } else {
                    if (data.action.toString().toLowerCase() == 'reload' ) {
                        window.location.reload();
                    } else {
                        $j.fancybox.close();
                    }                    
                }                
            },
            error   : function() {
                alert('General Error');
                $j.fancybox.close();
            }
        });
    };
    
    $.fn.locationWarning = function(isHome) {
        
        var $this = $(this);
        $(this).height(30);
        $(this).find('.container').show();
        $(this).find('#rs_alert_close_link').click(function() {
            // Imposto il cookie
            event.preventDefault();
            event.stopPropagation();
            $.ajax({url: $(this).attr('href')});
            $this.height(0);
            $this.find('div').css({'display': 'none'});
        });
        
        $('#' + $(this).attr('id') + ' .mpslocation-change-country').click( function(event) {
            event.preventDefault();
            event.stopPropagation();
            $.fn.selectCountry({'url': $(this).attr('href'),
                             'countryCode': '',
                             'home': (isHome) ? 1 :0 ,
                             'urlref': window.btoa(unescape(encodeURIComponent( window.location ))),
                             'enableEsc': true});
        });
        
    }
})(jQuery, this);

