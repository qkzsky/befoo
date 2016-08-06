/**
 * Created by kuangzhiqiang on 2016/1/19 0019.
 */
// 显示固定信息条 (type=success, error, warning, info, box)
var animateTimer;
function showAlertMsg(type, html, show_time, show_close_btn, callback_func) {
    var type = arguments[0] ? arguments[0] : 'success';
    var html = arguments[1] ? arguments[1] : '提交成功';
    var callback_func = arguments[4] ? arguments[4] : (typeof arguments[3] === "function" ? arguments[3] : (typeof arguments[2] === "function" ? arguments[2] : null));
    var show_time = (typeof arguments[2] === "number") ? arguments[2] : 3000;
    var show_close_btn = (typeof arguments[3] === "boolean") ? arguments[3] : false;

    if (show_time === 0) {
        show_close_btn = true;
    }
    var close_html = (show_close_btn == true) ? '<a href="javascript:;" class="x-fixed-msg-close-btn" onclick="removeAlertMsg();">关闭</a>' : '';
    $('.x-fixed-msg').remove();

    // 生成top和left都为负的不可见bom
    $('body').prepend(
        '<div class="x-fixed-msg x-fixed-msg-' + type + '"><div class="x-fixed-msg-inner">' +
        close_html +
        '<div class="p">' + html + '</div>' +
        '</div></div>'
    );

    // 获取宽高
    $('.x-fixed-msg').css({
        'bottom': '-' + (parseInt($('.x-fixed-msg').height()) + 10) + 'px',
        'left': '50%',
        'margin-left': '-' + parseInt($('.x-fixed-msg').width()) / 2 + 'px'
    });

    $('.x-fixed-msg').animate({'bottom': 50, opacity: 1}, 'fast', "linear", function () {
        animateTimer = setTimeout(function () {
            $('.x-fixed-msg').animate({opacity: 0}, '', "linear", callback_func);
        }, show_time);
    });
}

// 移出固定信息条
function removeAlertMsg() {
    $('.x-fixed-msg').remove();
    clearTimeout(animateTimer);
}

// 显示android的提示
function showAndroidToast(toast, callback) {
    if (typeof Android != "undefined") {
        Android.showToast(toast);
    }

    if (typeof callback != "undefined" && typeof callback == "function") {
        callback();
    }
}

// 默认调用的toast
function toast(message, type, callback) {
    if (typeof Android === "undefined") {
        var type = arguments[1] ? arguments[1] : 'success';
        showAlertMsg(type, message, 3000, 0, callback)
    } else {
        showAndroidToast(message, callback);
    }
}