require(["jquery"], function ($) {

    $(".sorting, .sorting_asc, .sorting_desc, .sorting_asc_disabled, .sorting_desc_disabled").on("selectstart", function () {
        return false;
    });

    $("table.dataTable thead").on("click", ".sorting, .sorting_asc, .sorting_desc", function () {
        var self = $(this);
        var sort_class = self.hasClass("sorting_asc") ? "sorting_desc" : "sorting_asc";
        self.removeClass("sorting sorting_asc sorting_desc")
            .addClass(sort_class)
            .siblings(".sorting_asc, .sorting_desc")
            .removeClass("sorting_asc sorting_desc")
            .addClass("sorting");

        var sort_option = [
            self.data("field"), sort_class.slice(8)
        ];
        self.closest("table.dataTable")
            .data("sort", sort_option)
            .trigger("initTableData");
    });

    $("table.dataTable").each(function () {
        var $table = $(this);
        var $pagination = $table.next(".m-pagination");
        if (!$pagination.length) {
            $pagination = $("<div/>").addClass("m-pagination");
            $table.after($pagination);
        }

        var $dataEmpty = $table.find(".dataTable_empty");
        if ($dataEmpty.length) {
            var colNum = $table.find("thead > tr > th").length;
            $dataEmpty.attr("colspan", colNum);
        }
    });

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