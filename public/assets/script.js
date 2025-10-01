$(function () {
    const $form = $('#news-form');
    if (!$form.length) return;

    const $formTitle = $('#form-title');
    const $cancel = $('#cancel-edit');
    const $actionUrlCreate = '/?r=news/create';
    const $actionUrlUpdate = '/?r=news/update';
    const $id = $form.find('[name="id"]');
    const $title = $form.find('[name="title"]');
    const $desc = $form.find('[name="description"]');
    const $submit = $('#submit-btn');

    function setForm(mode, data={}) {
        if (mode==='edit') {
            $id.val(data.id); $title.val(data.title); $desc.val(data.description);
            $form.attr('action', $actionUrlUpdate);
            $formTitle.text('Edit News'); $submit.text('Save'); $cancel.show().focus();
        } else {
            $id.val(''); $title.val(''); $desc.val('');
            $form.attr('action', $actionUrlCreate);
            $formTitle.text('Create News'); $submit.text('Create'); $cancel.hide();
        }
    }
    $('#news-list').on('click','.edit-btn',function(){
        const $li=$(this).closest('li');
        setForm('edit',{ id:$li.data('id'), title:$li.data('title'), description:$li.data('description') });
    });
    $cancel.on('click', ()=> setForm('create'));
});
