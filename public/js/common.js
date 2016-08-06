// BootstrapDialog
BootstrapDialog.configDefaultOptions({tabindex: 1});
BootstrapDialog.closestDialog = function ($this) {
    var dialog_id = $this.closest(".bootstrap-dialog").attr("id");
    return this.getDialog(dialog_id);
};

// select2
$.fn.select2.defaults.set("width", "100%");
$("select.select2").select2();

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

(function ($) {
    $.fn.extend({
        // 光标位置插入
        insertAtCaret: function (myValue) {
            var $t = $(this)[0];
            if (document.selection) {
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            } else if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
            return this;
        },
        closestBsDialog: function () {
            var dialog_id = $(this).closest(".bootstrap-dialog").attr("id");
            return BootstrapDialog.getDialog(dialog_id);
        }
    })
})(jQuery);

function initDataTable() {
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
}

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

// initDataTable
initDataTable();

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