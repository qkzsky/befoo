/**
 * Created by kuangzhiqiang on 16/8/7.
 */

function isload(name) {
    var isload = false;
    var tags = {"js": "script", "css": "link"};
    var tagname = tags[name.split(".").pop()];
    if (tagname != undefined) {
        var elts = document.getElementsByTagName(tagname);
        for (var i in elts) {
            if ((elts[i].href && elts[i].href.toString().indexOf(name) != "-1") ||
                (elts[i].src && elts[i].src.toString().indexOf(name) != "-1")) {
                isload = true;
            }
        }
    }
    return isload;
}

requirejs.config({
    baseUrl: "/js/app/src",
    paths: {
        "md5": "/components/md5",
        "vue": "/components/vue/vue.min",
        "toastr": "/components/toastr/toastr.min",
        "moment": "/components/moment.min",
        "select2": "/components/select2/js/i18n/zh-CN",
        "metisMenu": "/components/metisMenu/metisMenu.min",
        "pagination": "/components/Mricode.Pagination/mricode.pagination.min",
        "jquery": "/components/jquery/jquery-2.1.4.min",
        "jquery-ajaxfileupload": "/components/jquery/jquery.ajaxfileupload",
        "bootstrap": "/components/bootstrap/3.3.5/js/bootstrap.min",
        "bootstrap-dialog": "/components/bootstrap3-dialog/js/bootstrap-dialog.min",
        "bs-editable": "/components/bootstrap3-editable/js/bootstrap-editable.min",
        "bs-validator": "/components/bootstrap-validator/js/language/zh_CN",
        "bs-filestyle": "/components/bootstrap-filestyle/bootstrap-filestyle.min",
        "bs-daterangepicker": "/components/bootstrap-daterangepicker/daterangepicker",
        "ztree": "/components/zTree/js/jquery.ztree.excheck-3.5",
        "zeroclipboard": "/components/zeroclipboard/ZeroClipboard.min",


        "vue-private": "/js/private/vue-private",
        "jquery-private": "/js/private/jquery-private",
        "toastr-private": "/js/private/toastr-private",
        "select2-private": "/js/private/select2-private",
        "pagination-private": "/js/private/pagination-private",
        "bs-dialog-private": "/js/private/bs-dialog-private"
    },
    map: {
        '*': {
            'css': '/components/require-css/css.min.js',
            'vue': 'vue-private',
            'jquery': 'jquery-private',
            'toastr': 'toastr-private',
            'select2': 'select2-private',
            'pagination': 'pagination-private',
            'bs-dialog': 'bs-dialog-private'
        },
        'vue-private': {'vue': 'vue'},
        'jquery-private': {'jquery': 'jquery'},
        'toastr-private': {'toastr': 'toastr'},
        'select2-private': {'select2': 'toastr'},
        'pagination-private': {'pagination': 'pagination'}
    },
    shim: {
        "toastr": {
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
                var l = [
                    "jquery"
                ];
                if (!isload("bootstrap.min.css")) {
                    l.push("css!/components/bootstrap/3.3.5/css/bootstrap.min.css");
                }
                return l;
            }()
        },
        "pagination": {
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
        "select2": {
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
