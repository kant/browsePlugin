/* carousel */
$(function () {
    // turn carousel on only on medium and large screens
    if ($(window).width() >= 975) {
        $('.carousel').carousel();
    }
    // refresh page if user changes page width
    function refresh() {
        ww = $(window).width();
        var w =  ww<limit ? (location.reload(true)) :  ( ww>limit ? (location.reload(true)) : ww=limit );
    }

    var ww = $(window).width();
    var limit = 974;

    var tOut;
    $(window).resize(function() {
        var resW = $(window).width();
        clearTimeout(tOut);
        if ( (ww>limit && resW<limit) || (ww<limit && resW>limit) ) {
            tOut = setTimeout(refresh, 0);
        }
    });
});