<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<html>
    <head>
         <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <style>
            body {margin: 0px}
            #mediaplayer_wrapper { margin: auto; }
            .wrapper {padding:0px; min-height: 970px}
            .page {min-height: 300px; background: #fff;}
            .container { position:relative; background-color: red}
        </style>
    </head>
    <body>
<!--        <div id="dbg">
            <input type="text-area"/>
        </div>-->
        <div class="wrapper">

            <div class="container">
                <div id="img-home-a" style="position: relative; height: 100%; width: 100%" >
                    <div id="mediaplayer">...</div>
                </div>      
    <script type="text/javascript" src="http://www.marcoma.com:8080/testcm/skin/frontend/default/corto/js/jwplayer.js"></script>
    <script type="text/javascript">
      jwplayer('mediaplayer').setup({
            modes: [
                    { type: 'html5' },
                    { type: 'flash', src: 'http://www.marcoma.com:8080/testcm/skin/frontend/default/corto/js/player.swf' }
                ],
        //'players': {type:"html5"},
        //'flashplayer': 'http://dev.autelfarm.net/corto/skin/frontend/default/corto/jsplayer.swf',
        'id': 'playerID',
        'width': '100%',
        'height': '100%',
        'file': 'http://www.marcoma.com:8080/testcm/media/video/home/CortoHome.mp4',
        'controlbar': 'none',
        "autostart": true,
        "loop": true,
//        "events" : {
//            "onResize" : function (event) {
//                console.log(event);
//            }
//        },
//        "stretching" : "uniform"
        "stretching" : "fill"
      });
      

    </script>         

            </div>
            <div class="page">
                &nbsp;
            </div>
        </div>
    </body>

    <script>
        $(window).load(function () {
            $(window).trigger("resize");            
        });
        
        $(window).resize(function () {
            $(".container").height($(window).height());
            $(".container").width($(window).width());
            console.log("Altezza Container="+$(".container").height());
            console.log("dimensioni="+$("object#mediaplayer").height()+"/"+$("object#mediaplayer").width());
            console.log("Altezza Windows="+$(window).height());
        })
    </script>    

</html>
