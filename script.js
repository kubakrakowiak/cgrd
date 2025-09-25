$(function () {
    const $form = $('#news-form');
    const $formTitle = $('#form-title');
    const $cancel = $('#cancel-edit');
    const $action = $form.find('[name="action"]');
    const $id = $form.find('[name="id"]');
    const $title = $form.find('[name="title"]');
    const $desc = $form.find('[name="description"]');
    const $submit = $('#submit-btn');

    function setForm(mode, data = {}) {
        if (mode === 'edit') {
            $id.val(data.id);
            $title.val(data.title);
            $desc.val(data.description);
            $action.val('update');
            $formTitle.text('Edit News');
            $submit.text('Save');
            $cancel.show().focus();
        } else {
            $id.val('');
            $title.val('');
            $desc.val('');
            $action.val('create');
            $formTitle.text('Create News');
            $submit.text('Create');
            $cancel.hide();
        }
    }

    $('#news-list').on('click', '.edit-btn', function () {
        const $li = $(this).closest('li');
        setForm('edit', {
            id: $li.data('id'),
            title: $li.data('title'),
            description: $li.data('description'),
        });
    });

    $cancel.on('click', () => setForm('create'));
});
