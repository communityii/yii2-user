var kvStatusBatchUpdate = function() {
    var $btn = $('#btn-batch-update'), config = kvBatchUpdateConfig;
    
    $btn.off('click').on('click', function() {
        var keys = $('#user-grid').yiiGridView('getSelectedRows'), 
            $el = $('#batch-status'), status = $el.val();
        if (!status) {
            alert(config.alert2);
            return;
        }
        if (!keys.length) {
            alert(config.alert1);
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
                success: function() {
                    $('#user-grid').yiiGridView('applyFilter');
                }
            });
        }
    });
};
$('#user-grid-pjax').on('pjax:complete', function() {
    var $el = $('#batch-status'), data = window[$el.attr('data-krajee-select2')] || {};
    $el.on('select2:opening', initS2Open).on('select2:unselecting', initS2Unselect);
    $.when($el.select2(data)).done(initS2Loading('batch-status', '.select2-container--krajee', '', true));
    kvStatusBatchUpdate();
});
kvStatusBatchUpdate();