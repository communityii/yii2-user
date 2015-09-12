/*!
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 * @see https://github.com/communityii/yii2-user
 */
var kvStatusBatchUpdate = function () {
    "use strict";
    (function ($) {
        var $btn = $('#btn-batch-update'), config = window['kvBatchUpdateConfig'], $alert = $(config.elOut),
            close = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '<span aria-hidden="true">&times;</span>\n' + '</button>',
            notify = function (type, content) {
                $alert.html('<div class="alert alert-' + type + ' fade in">' + close + content + '</div>');
            };

        $btn.off('click').on('click', function () {
            var keys = $('#user-grid').yiiGridView('getSelectedRows'),
                $el = $('#batch-status'), status = $el.val();
            $alert.html('');
            if (!status) {
                notify('danger', config.alert2);
                return;
            }
            if (!keys.length) {
                notify('danger', config.alert1);
                return;
            }
            if (window.confirm(config.confirmMsg) !== false) {
                $.ajax({
                    url: config.url,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        keys: keys,
                        status: status
                    },
                    success: function (data) {
                        $('#user-grid').yiiGridView('applyFilter');
                        setTimeout(function () {
                            notify(data.status, data.message);
                        }, 1000);
                    }
                });
            }
        });
    })
    (window.jQuery);
};
kvStatusBatchUpdate();