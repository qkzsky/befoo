/**
 * Created by kuangzhiqiang on 16/8/7.
 */
define(['vue'], function (Vue) {
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