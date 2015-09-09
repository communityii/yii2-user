var kvStatusBatchUpdate = function() {
    var $btn = $('#btn-batch-update'), config = kvBatchUpdateConfig, $el = $(config.elOut),
        close = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
        '<span aria-hidden="true">&times;</span>\n' + '</button>',
        notify = function(type, content) {
            $el.html('<div class="alert alert-' + type + ' fade in">' + close + content + '</div>');
        };
    
    $btn.off('click').on('click', function() {
        var keys = $('#user-grid').yiiGridView('getSelectedRows'), 
            $el = $('#batch-status'), status = $el.val();
        if (!status) {
            notify('danger', config.alert2);
            return;
        }
        if (!keys.length) {
            notify('danger', config.alert1);
            return;
        }
        if (confirm(config.confirmMsg)) {
            $.ajax({
                url: config.url, 
                type: 'post',
                dataType: 'json',
                data: {
                    keys: keys,
                    status: status
                },
                success: function(data) {
                    $('#user-grid').yiiGridView('applyFilter');
                    setTimeout(function() {
                        notify('success', data.message);
                    }, 1000);
                }
            });
        }
    });
};
kvStatusBatchUpdate();