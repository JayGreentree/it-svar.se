<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="Lists">

     <div class="btitle" id="actionbar">
        <h2><?=lang('form:lists')?></h2>
        <a href="<?=$base_url?>&method=create_list" class="abtn add"><span><?=lang('form:list_new')?></span></a>
        <a href="<?=$base_url?>&method=create_list&list_id=" class="abtn edit btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:list_edit')?></span></a>
        <a href="<?=$base_url?>&method=update_list&delete=yes&list_id=" class="abtn delete btn-action" data-multiple="no" data-followurl='yes'><span><?=lang('form:list_del')?></span></a>
    </div>

    <div style="padding:10px 20px">
        <table cellpadding="0" cellspacing="0" border="0" class="DFTable selectable" data-multicheck="no">
        <thead>
          <tr>
            <th style="width:30px"><?=lang('form:id')?></th>
            <th><?=lang('form:list_label')?></th>
          </tr>
        </thead>
        <tbody>
            <?php if (empty($lists) === TRUE):?><tr><td colspan="99"><?=lang('form:no_lists')?></td></tr> <?php endif;?>
            <?php foreach($lists as $list): ?>
            <tr id="<?=$list->list_id?>">
                <td><?=$list->list_id?></td>
                <td><?=$list->list_label?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
      </table>
    </div>

</div><!--fbody-->
<?php echo $this->view('mcp/_footer'); ?>