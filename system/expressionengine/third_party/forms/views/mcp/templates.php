<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="Templates">
  <div class="btitle" id="actionbar">
      <h2><?=lang('form:templates')?></h2>
      <a href="<?=$base_url?>&method=create_template" class="abtn add"><span><?=lang('form:tmpl_new')?></span></a>
      <a href="<?=$base_url?>&method=create_template&template_id=" class="abtn edit btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:tmpl_edit')?></span></a>
      <a href="<?=$base_url?>&method=update_template&delete=yes&template_id=" class="abtn delete btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:tmpl_del')?></span></a>
  </div>

  <div style="padding:10px 20px">
    <table cellpadding="0" cellspacing="0" border="0" class="DFTable selectable" data-multicheck="no">
      <thead>
        <tr>
          <th style="width:30px"><?=lang('form:id')?></th>
          <th><?=lang('form:tmpl_label')?></th>
          <th><?=lang('form:tmpl_name')?></th>
          <th><?=lang('form:type')?></th>
        </tr>
      </thead>
      <tbody>
          <?php if (empty($templates) === TRUE):?><tr><td colspan="99"><?=lang('form:no_templates')?></td></tr> <?php endif;?>
          <?php foreach($templates as $tmpl):?>
          <tr id="<?=$tmpl->template_id?>">
              <td><?=$tmpl->template_id?></td>
              <td><?=$tmpl->template_label?></td>
              <td><?=$tmpl->template_name?></td>
              <td><?=lang('form:tmpl:' . $tmpl->template_type)?></td>
          </tr>
          <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<?php echo $this->view('mcp/_footer'); ?>