require.config(
{
    paths: {
        domReady: 'require-plugins/domReady',
        jQuery: 'http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min'
    }
});
require(["jQuery", "domReady!"],

function(jQuery, domReady)
{
    //This function will be called when all the dependencies
    //listed above are loaded. Note that this function could
    //be called before the page is loaded

    // Log that jquery was loaded into the global name-space.
    console.log("DOM Ready !");
    console.log("jQuery", $.fn.jquery, "loaded!");

    $('article').css('min-height', $(window).height() + 'px');

    require(["modules/googleAnalytics","modules/bootstrap", "lib/modal"], function()
    {
        $(window).resize(function(event)
        {
            $('article').css('min-height', $(window).height() + 'px');
        });

        $('.video').magnificPopup(
        {
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: true,
            fixedContentPos: false
        });
    });
});