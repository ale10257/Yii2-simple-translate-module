$(function () {
    let modalBlock = $('#translate-modal');
    let obj, array, id, value, url;
    function getForm (url, $this) {
        obj = {
            id: $this.data('key'),
            action : $this.data('action'),
        };
        $.post(url, obj, function (data) {
            modalBlock.find('.modal-body').html(data.form);
            modalBlock.modal({show: true});
        });
    }
    $('.translate-td').click(function () {
        url = modalBlock.data('url');
        getForm (url, $(this));
    });
    $('#insert-term').click(function (e) {
        e.preventDefault();
        getForm (this.href, $(this));
    });
    $(document).on('submit', '#form-translate', function () {
        $.post(this.action, $(this).serialize(), function (data) {
            if (data.success) {
                array = data.success;
                for (let i = 0; i < array.length; i++) {
                    id = array[i].id;
                    value = array[i].value;
                    $(id).html(value);
                }
                modalBlock.modal('hide');
            }
            if (data.error) {
                alert(data.error);
            }
            if (data.reload) {
                location.reload();
            }
        });
        return false;
    });
});