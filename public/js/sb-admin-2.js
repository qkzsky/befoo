//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
require(["jquery", "bootstrap", "metisMenu"], function () {
    $(function () {

        $('#side-menu').metisMenu();

        $(window).bind("load resize", function () {
            var topOffset = 50;
            var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
            if (width < 768) {
                $('div.navbar-collapse').addClass('collapse');
                topOffset = 100; // 2-row-menu
            } else {
                $('div.navbar-collapse').removeClass('collapse');
            }

            var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            if (height < 1) height = 1;
            if (height > topOffset) {
                $("#page-wrapper").css("min-height", (height) + "px");
            }
        });

        var url = window.location.href;
        var element = $('ul.nav a').filter(function () {
            return this.href === url || url.indexOf(this.href) === 0;
        }).addClass('active').closest('.nav').addClass('in').parent();
        if (element.is('li')) {
            element.addClass('active');
        }
    });
});
