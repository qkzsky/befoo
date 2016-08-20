/**
 * Created by kuangzhiqiang on 16/8/7.
 */
define(['jquery', 'pagination'], function ($) {
    $.fn.pagination.defaults = ({
        showInfo: true,
        showJump: true,
        showPageSizes: true,
        pageSizeItems: [20, 50, 100, 200],
        pageBtnCount: 7,
        firstBtnText: '首页',
        lastBtnText: '尾页',
        prevBtnText: '上一页',
        nextBtnText: '下一页',
        jumpBtnText: '跳转',
        infoFormat: '第{start} ~ {end}条，共{total}条',
        noInfoText: '0条记录'
    });

    $("[pg-table]").on("selectstart", ".sort, .sort-asc, .sort-desc", function () {
        return false;
    });

    $("[pg-table] thead").on("click", ".sort, .sort-asc, .sort-desc", function () {
        var self = $(this);
        var sort_class = self.hasClass("sort-asc") ? "sort-desc" : "sort-asc";
        self.removeClass("sort sort-asc sort-desc")
            .addClass(sort_class)
            .siblings(".sort-asc, .sort-desc")
            .removeClass("sort-asc sort-desc")
            .addClass("sort");

        var sort_option = [
            self.data("field"), sort_class.slice(5)
        ];
        self.closest("[pg-table]")
            .data("sort", sort_option)
            .trigger("pg-init");
    });

    $(document).on("pg-init", "[pg-table]", function () {
        var $this = $(this);
        var $pagination = $this.next(".m-pagination");
        if (!$pagination.length) {
            $pagination = $("<div/>").addClass("m-pagination");
            $this.after($pagination);
        }
        var currentPageIndex = 0;
        if ($pagination.data('pagination')) {
            currentPageIndex = $pagination.data('pagination').currentPageIndex;
            $pagination.pagination('destroy');
        }
        var $tableEmpty = $this.find(".table-empty");
        if ($tableEmpty.length) {
            var colNum = $this.find("thead > tr > th").length;
            $tableEmpty.attr("colspan", colNum);
        }

        var $form = $($this.data("pgForm"));
        var params = $form.serializeArray();
        if ($this.data("sort")) {
            params.push({name: "sort", value: $this.data("sort")});
        }
        $pagination.pagination({
            pageSize: $this.data("pgSize") ? $this.data("pgSize") : 20,
            pageIndex: currentPageIndex,
            remote: {
                url: $form.attr("action"),
                params: params,
                success: function (result, pageIndex) {
                    $this.data("pgData", result);
                    $this.trigger("pg-loaded")
                }
            }
        });
    });

    $(document).on("submit", "[pg-form]", function () {
        if ($(this).data("pgTable")) {
            $($(this).data("pgTable")).trigger("pg-init");
            return false;
        }
    })
});