require(["jquery"], function ($) {
    // close popover
    $('body').on('click', function (e) {
        var target = $(e.target);
        $('[data-toggle="popover"]').each(function () {
            var $this = $(this);
            if (!$this.is(target) && $this.has(target).length === 0 && $('.popover').has(target).length === 0) {
                var popoverElement = $this.data('bs.popover').tip();
                var popoverWasVisible = popoverElement.is(':visible');

                if (popoverWasVisible) {
                    $this.popover('hide');
                    // double clicking required to reshow the popover if it was open, so perform one click now
                    var inState = $this.data('bs.popover').inState;
                    for (var i in inState) {
                        inState[i] = false;
                    }
                }
            }
        });
    });
});