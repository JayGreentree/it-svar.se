<?php if (empty($variables)): ?>

	<p class="alert"><?=lang('no_variables_found')?></p>

<?php else: ?>

<form action="<?=$base_url?>&amp;method=save_list" method="post">
	<div>
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />
	</div>
	<table cellpadding="0" cellspacing="0" class="mainTable" id="low-list-vars">
		<colgroup>
			<col style="width:3%" />
			<col style="width:19%" />
			<col style="width:19%" />
			<col style="width:19%" />
			<col style="width:19%" />
			<col style="width:5%" />
			<col style="width:5%" />
			<col style="width:5%" />
			<col style="width:5%" />
			<col style="width:1%" />
		</colgroup>
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col"><?=lang('variable_name')?></th>
				<th scope="col"><?=lang('variable_label')?></th>
				<th scope="col"><?=lang('variable_group')?></th>
				<th scope="col"><?=lang('variable_type')?></th>
				<th scope="col" style="text-align:center"><?=lang('is_hidden_th')?></th>
				<th scope="col" style="text-align:center"><?=lang('early_parsing_th')?></th>
				<th scope="col" style="text-align:center"><?=lang('save_as_file_th')?></th>
				<th scope="col" style="text-align:center"><?=lang('clone')?></th>
				<th scope="col" style="text-align:center"><input type="checkbox" id="low-toggle-all" /></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($variables AS $row): ?>
			<tr class="<?=low_zebra()?>">
				<td><?=$row['variable_id']?></td>
				<td><a href="<?=sprintf($edit_var_url,$row['variable_id'],'manage')?>" class="low-var-name"><?=$row['variable_name']?></a></td>
				<td><?=$row['variable_label']?></td>
				<td><a href="<?=sprintf($edit_group_url,$row['group_id'],'manage')?>"><?=htmlspecialchars($groups[$row['group_id']])?></a></td>
				<td style="white-space:nowrap"><?=$row['variable_type']?></td>
				<td style="text-align:center"><?=$row['is_hidden']?></td>
				<td style="text-align:center"><?=$row['early_parsing']?></td>
				<td style="text-align:center"><?=$row['save_as_file']?></td>
				<td><a href="<?=sprintf($edit_var_url, 'new', 'manage')?>&amp;clone=<?=$row['variable_id']?>"
					class="clone" title="<?=lang('clone')?> <?=$row['variable_name']?>">
					<?=lang('clone')?>
				</a></td>
				<td><input type="checkbox" id="var_<?=$row['variable_id']?>" name="toggle[]" value="<?=$row['variable_id']?>" /></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="box" style="overflow:hidden">

		<div style="float:right">
			<label for="select_action"><?=lang('with_selected')?></label>
			<select name="action" id="select_action">
				<option value=""></option>
				<option value="delete"><?=lang('delete')?></option>
				<optgroup label="<?=lang('show-hide')?>">
					<option value="show"><?=lang('show')?></option>
					<option value="hide"><?=lang('hide')?></option>
				</optgroup>
				<?php if($settings['register_globals'] == 'y'): ?>
					<optgroup label="<?=lang('early_parsing')?>">
						<option value="enable_early_parsing"><?=lang('enable_early_parsing')?></option>
						<option value="disable_early_parsing"><?=lang('disable_early_parsing')?></option>
					</optgroup>
				<?php endif; ?>
				<?php if($settings['save_as_files'] == 'y'): ?>
					<optgroup label="<?=lang('save_as_file')?>">
						<option value="enable_save_as_file"><?=lang('enable_save_as_file')?></option>
						<option value="disable_save_as_file"><?=lang('disable_save_as_file')?></option>
					</optgroup>
				<?php endif; ?>
				<optgroup label="<?=lang('change_group_to')?>">
					<?php foreach($groups AS $vg_id => $vg_label): ?>
						<option value="<?=$vg_id?>"><?=$vg_label?></option>
					<?php endforeach; ?>
				</optgroup>
				<optgroup label="<?=lang('change_type_to')?>">
					<?php foreach($types AS $type => $obj): ?>
						<option value="<?=$type?>"><?=$obj->info['name']?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
			<button type="submit" class="submit"><?=lang('submit')?></button>
		</div>

	</div>
</form>
<?php endif; ?>