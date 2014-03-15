jQuery(function($) {
    $(window).resize(function() {
        $(".guest-body").css("margin-top", (($(window).height() - $(".guest-body").height()) / 2) + "px" );
        if($(".error-body").height() < $(window).height())
            $(".error-body").css("margin-top", (($(window).height() - $(".error-body").height()) / 2) - 50 + "px" );
        else
            $(".error-body").css({"margin-top": "30px", "margin-bottom": "30px"});
    }).resize();
});