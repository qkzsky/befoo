Vue.filter("datetime", function (v) {
    var date = new Date(v * 1000),
        Y = date.getFullYear(),
        M = (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1),
        D = (date.getDate() < 10 ? '0' : '') + date.getDate(),
        h = (date.getHours() < 10 ? '0' : '') + date.getHours(),
        m = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(),
        s = (date.getSeconds() < 10 ? '0' : '') + date.getSeconds();
    return Y + "-" + M + "-" + D + " " + h + ":" + m + ":" + s;
});

Vue.filter("when", function (v, a, b) {
    return v ? a : b;
});

Vue.filter("switch", function (v) {
    var cls = 'label';
    cls += ' ' + (v === 'Y' ? 'label-success' : 'label-danger');
    return '<label class="' + cls + '">' + v + '</label>';
});

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

function initDataTable() {
    $("table.dataTable").each(function(){
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
}

$(function () {
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


    BootstrapDialog.configDefaultOptions({tabindex: 1});
    // select2
    $.fn.select2.defaults.set("width", "100%");
    $("select.select2").select2();

    // initDataTable
    initDataTable();
});