<?php echo $this->view('menu'); ?>
<div class="dtable" style="margin:0">
	<h2 style="padding:9px 9px 5px">
		<?=lang('ed:editor_conf')?>
		<a href="<?=$base_url?>&method=new_configuration" class="abtn add" style="margin:0 0 0 10px;"><span><?=lang('ed:new_conf')?></span></a>
	</h2>
</div>

<div id="dbody">

	<div style="padding:10px 20px">
		<table cellpadding="0" cellspacing="0" border="0" class="DFTable" style="width:50%">
			<thead>
				<tr>
					<th><?=lang('ed:name')?></th>
					<th style="width:100px"></th>
					<th style="width:100px"></th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($editors) == TRUE):?>
				<tr>
					<td colspan="5"><?=lang('ed:no_editors')?></td>
				</tr>
				<?php endif;?>

				<?php foreach($editors as $editor):?>
				<tr>
					<td><a href="<?=$base_url?>&method=new_configuration&config_id=<?=$editor->config_id?>"><?=$editor->config_label?></a></td>
					<td><a href="<?=$base_url?>&method=clone_configuration&config_id=<?=$editor->config_id?>" class="abtn clone" style="position: relative;top:0;"><span><?=lang('ed:clone')?></span></a></td>
					<td><a href="<?=$base_url?>&method=update_configuration&delete=yes&config_id=<?=$editor->config_id?>" class="abtn delete" style="position: relative;top:0;"><span><?=lang('ed:delete')?></span></a></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>

</div>
