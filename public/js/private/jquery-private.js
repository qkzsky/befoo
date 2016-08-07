/**
 * Created by kuangzhiqiang on 16/8/7.
 */
define(['jquery'], function ($) {
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
    });
    return $;
});