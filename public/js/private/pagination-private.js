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
});