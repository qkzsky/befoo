/**
 * Created by kuangzhiqiang on 16/8/7.
 */
define(['toastr'], function (toastr) {
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