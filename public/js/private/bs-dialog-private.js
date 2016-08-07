/**
 * Created by kuangzhiqiang on 16/8/7.
 */
define(['bootstrap-dialog'], function (BootstrapDialog) {
    BootstrapDialog.configDefaultOptions({tabindex: 1});
    BootstrapDialog.closestDialog = function ($this) {
        var dialog_id = $this.closest(".bootstrap-dialog").attr("id");
        return this.getDialog(dialog_id);
    };

    return BootstrapDialog;
});