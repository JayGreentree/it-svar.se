<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="FormsHome">
    <div class="btitle" id="actionbar">
        <h2><?=lang('forms')?></h2>
        <a href="<?=$base_url?>&method=create_form" class="abtn add"><span><?=lang('form:form_new')?></span></a>
        <a href="<?=$base_url?>&method=view_form&form_id=" class="abtn pages btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:view_submissions')?></span></a>
        <a href="<?=$base_url?>&method=create_form&form_id=" class="abtn edit btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:edit_form')?></span></a>
        <a href="<?=$base_url?>&method=delete_form&form_id=" class="abtn delete btn-action DeleteForm" data-multiple="no"><span><?=lang('form:delete_form')?></span></a>
        <a href="<?=$base_url?>&method=duplicate_form&form_id=" class="abtn dupe btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:dupe_form')?></span></a>
    </div>

    <div style="padding:10px 20px">
        <table cellpadding="0" cellspacing="0" border="0" class="DFTable selectable" data-multicheck="no">
        <thead>
          <tr>
            <th style="width:30px"><?=lang('form:id')?></th>
            <th><?=lang('form')?></th>
            <th><?=lang('form:form_url_title')?></th>
            <th><?=lang('form:member')?></th>
            <th style="width:130px"><?=lang('form:type')?></th>
            <th style="width:70px"><?=lang('form:submissions')?></th>
            <th style="width:130px"><?=lang('form:date_created')?></th>
            <th style="width:130px"><?=lang('form:last_entry')?></th>
          </tr>
        </thead>
        <tbody>
            <?php if (empty($forms) === TRUE):?><tr><td colspan="99"><?=lang('form:no_forms')?></td></tr> <?php endif;?>
            <?php foreach($forms as $form):?>
            <tr id="<?=$form->form_id?>">
                <td><?=$form->form_id?></td>
                <td><?=$form->form_title?></td>
                <td><?=$form->form_url_title?></td>
                <td><?=$form->screen_name?></td>
                <td>
                    <?php if ($form->form_type == 'normal'):?>
                    <strong style="color:darkblue"><?=lang('form:salone')?></strong>
                    <?php elseif ($form->form_type == 'entry'):?>
                    <strong style="color:darkgreen"><?=lang('form:entry_linked')?></strong>
                    <?php endif;?>
                </td>
                <td><?=$form->total_submissions?></td>
                <td><?=$this->forms_helper->formatDate('%d-%M-%Y %g:%i %A', $form->date_created)?></td>
                <td><?=(($form->date_last_entry != FALSE) ? $this->forms_helper->formatDate('%d-%M-%Y %g:%i %A', $form->date_last_entry) : ''); ?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
      </table>
    </div>
</div><!--fbody-->


<div id="del_form" class="modal fade" style="display:none">
    <div class="modal-header">
        <button class="close" data-dismiss="modal">×</button>
        <h3><?=lang('form:delete')?></h3>
    </div>
    <div class="modal-body">
        <p></p>
    </div>
    <div class="modal-footer">
        <button href="#" class="btn" data-dismiss="modal"><?=lang('form:close')?></button>
        <button href="#" class="btn btn-primary"><?=lang('form:delete');?></button>
    </div>
</div>

<br clear="all">
<?php echo $this->view('mcp/_footer'); ?>
