;(function ($) {

    'use strict'

    $(document).ready(function () {


        // Video Popup with FancyBox
        var fancy = $ (".fancybox");
        if(fancy.length) {
            fancy.fancybox({
                arrows: true,
                buttons: [
                    "zoom",
                    //"share",
                    "slideShow",
                    //"fullScreen",
                    //"download",
                    "thumbs",
                    "close"
                ],
                animationEffect: "zoom-in-out",
                transitionEffect: "zoom-in-out",
            });
        }


    });


})(jQuery)