/**
 * Created by kuangzhiqiang on 16/8/7.
 */
requirejs.config({
    baseUrl: "/js/app/src",
    map: {
        '*': {
            'css': '/components/require-css/css.min.js'
        }
    },
    paths: {
        "md5": "/components/md5",
        "o-vue": "/components/vue/vue.min",
        "o-toastr": "/components/toastr/toastr.min",
        "moment": "/components/moment.min",
        "o-select2": "/components/select2/js/i18n/zh-CN",
        "metisMenu": "/components/metisMenu/metisMenu.min",
        "o-pagination": "/components/Mricode.Pagination/mricode.pagination.min",
        "jquery": "/components/jquery/jquery-2.1.4.min",
        "jquery-ajaxfileupload": "/components/jquery.ajaxfileupload",
        "bootstrap": "/components/bootstrap/3.3.5/js/bootstrap.min",
        "bootstrap-dialog": "/components/bootstrap3-dialog/js/bootstrap-dialog.min",
        "bs-editable": "/components/bootstrap3-editable/js/bootstrap-editable.min",
        "bs-validator": "/components/bootstrap-validator/js/language/zh_CN",
        "bs-filestyle": "/components/bootstrap-filestyle/bootstrap-filestyle.min",
        "bs-daterangepicker": "/components/bootstrap-daterangepicker/daterangepicker",
        "ztree": "/components/zTree/js/jquery.ztree.excheck-3.5",
        "zeroclipboard": "/components/zeroclipboard/ZeroClipboard.min"
    },
    shim: {
        "o-toastr": {
            deps: [
                "css!/components/toastr/toastr.min.css"
            ]
        },
        "metisMenu": {
            deps: [
                "css!/components/metisMenu/metisMenu.min.css"
            ]
        },
        "bootstrap": {
            deps: function () {
                var l = [];
                var tag = document.getElementsByTagName("html")[0];
                if (window.getComputedStyle(tag, null).getPropertyValue("border-box") !== "") {
                    l.push("css!/components/bootstrap/3.3.5/css/bootstrap.min.css");
                }
                return l;
            }()
        },
        "o-pagination": {
            deps: [
                "css!/components/Mricode.Pagination/mricode.pagination.css"
            ]
        },
        "bootstrap-dialog": {
            deps: [
                "css!/components/bootstrap3-dialog/css/bootstrap-dialog.min.css"
            ]
        },
        "bs-validator": {
            deps: [
                "css!/components/bootstrap-validator/css/bootstrapValidator.min.css",
                "/components/bootstrap-validator/js/bootstrapValidator.min.js"
            ]
        },
        "bs-editable": {
            deps: [
                "css!/components/bootstrap3-editable/css/bootstrap-editable.css"
            ]
        },
        "bs-daterangepicker": {
            deps: [
                "css!/components/bootstrap-daterangepicker/daterangepicker.css"
            ]
        },
        "o-select2": {
            deps: [
                "css!/components/select2/css/select2.min.css",
                "/components/select2/js/select2.full.min.js"
            ]
        },
        "ztree": {
            deps: [
                "css!/components/zTree/css/metroStyle/metroStyle.css",
                "/components/zTree/js/jquery.ztree.core-3.5.js"
            ]
        }
    }
});

define("vue", ["o-vue"], function(Vue){
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

    return Vue;
});

define("bs-dialog", ["bootstrap-dialog"], function(BootstrapDialog){
    BootstrapDialog.configDefaultOptions({tabindex: 1});
    BootstrapDialog.closestDialog = function ($this) {
        var dialog_id = $this.closest(".bootstrap-dialog").attr("id");
        return this.getDialog(dialog_id);
    };
    return BootstrapDialog;
});

define("toastr", ["o-toastr"], function(toastr){
    toastr.options = {
        progressBar: true,
        preventDuplicates: false,
        showDuration: 300,
        hideDuration: 1000,
        timeOut: 4000,
        extendedTimeOut: 1000
    };
    return toastr;
});

define("pagination", ["jquery", "o-pagination"], function($){
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
});

define("select2", ["jquery", "o-select2"], function($){
    $.fn.select2.defaults.set("width", "100%");
});
