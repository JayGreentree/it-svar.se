<?php if ($errors): ?>
	<div class="low-alertbox">
		<ul><?php foreach($errors AS $msg): ?>
			<li><?=$msg?></li>
		<?php endforeach; ?></ul>
	</div>
<?php endif; ?>

<form method="post" action="<?=$base_url?>&amp;method=save_var" id="low-variable-form">
	<div>
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />
		<input type="hidden" name="variable_id" value="<?=$variable_id?>" />
		<input type="hidden" name="variable_order" value="<?=$variable_order?>" />
		<input type="hidden" name="from" value="<?=$from?>" />
	</div>
	<table cellpadding="0" cellspacing="0" class="mainTable">
		<colgroup>
			<col class="key" />
			<col class="val" />
		</colgroup>
		<thead>
			<tr>
				<th colspan="2"><?=lang('edit_variable')?> (#<?=$variable_id?>)</th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="low_variable_name"><span class="alert">*</span> <?=lang('variable_name')?></label>
					<div class="low-var-notes"><?=lang('variable_name_help')?></div>
				</td>
				<td>
					<input type="text" name="variable_name" id="low_variable_name" class="medium" value="<?=htmlspecialchars($variable_name)?>" />
					<?php if ($variable_id == 'new'): ?><script type="text/javascript"> document.getElementById('low_variable_name').focus(); </script><?php endif; ?>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="low_variable_label"><?=lang('variable_label')?></label>
					<div class="low-var-notes"><?=lang('variable_label_help')?></div>
				</td>
				<td>
					<input type="text" name="variable_label" id="low_variable_label" class="medium" value="<?=htmlspecialchars($variable_label)?>" />
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td style="vertical-align:top">
					<label class="low-label" for="low_variable_notes"><?=lang('variable_notes')?></label>
					<div class="low-var-notes"><?=lang('variable_notes_help')?></div>
				</td>
				<td>
					<textarea name="variable_notes" id="low_variable_notes" rows="4" cols="40"><?=htmlspecialchars($variable_notes)?></textarea>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="low_variable_group"><?=lang('variable_group')?></label>
				</td>
				<td>
					<select name="group_id" id="low_variable_group">
						<?php foreach($variable_groups AS $vg_id => $vg_label): ?>
							<option value="<?=$vg_id?>"<?php if($group_id == $vg_id): ?> selected="selected"<?php endif; ?>><?=htmlspecialchars($vg_label)?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<strong class="low-label"><?=lang('is_hidden')?></strong>
					<div class="low-var-notes"><?=lang('is_hidden_help')?></div>
				</td>
				<td>
					<label class="low-checkbox">
						<input type="checkbox" name="is_hidden" value="y"<?php if($is_hidden == 'y'):?> checked="checked"<?php endif; ?>>
						<?=lang('is_hidden_label')?>
					</label>
				</td>
			</tr>

			<tr class="<?=low_zebra()?>">
				<td>
					<strong class="low-label"><?=lang('early_parsing')?></strong>
					<div class="low-var-notes"><?=lang('early_parsing_help')?></div>
				</td>
				<td>
					<?php if ($settings['register_globals'] != 'n'): ?>
					<label class="low-checkbox">
						<input type="checkbox" name="early_parsing" id="early_parsing" value="y"<?php if($early_parsing == 'y'):?> checked="checked"<?php endif; ?>>
						<?=lang('enable_early_parsing')?>
					</label>
					<?php else: ?>
						<em><?=lang('early_parsing_disabled_msg')?></em>
					<?php endif; ?>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<strong class="low-label"><?=lang('save_as_file')?></strong>
					<div class="low-var-notes"><?=lang('save_as_file_help')?></div>
				</td>
				<td>
					<?php if ($settings['save_as_files'] == 'y'): ?>
					<label class="low-checkbox">
						<input type="checkbox" name="save_as_file" id="save_as_file" value="y"<?php if($save_as_file == 'y'):?> checked="checked"<?php endif; ?>>
						<?=lang('save_as_file_label')?>
					</label>
					<?php else: ?>
						<em><?=lang('save_as_file_disabled_msg')?></em>
					<?php endif; ?>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="low-select-type"><?=lang('variable_type')?></label>
					<div class="low-var-notes"><?=lang('variable_type_help')?></div>
				</td>
				<td>
					<select name="variable_type" id="low-select-type">
					<?php foreach($types AS $type => $row): ?>
						<option value="<?=$type?>"<?php if ($type == $variable_type): ?> selected="selected"<?php endif; ?>><?=$row['name']?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>

	<?php foreach($types AS $type => $row): ?>
		<?php if (empty($row['settings'])) continue; ?>
		<table cellpadding="0" cellspacing="0" class="mainTable low-var-type" id="<?=$type?>"<?php if($variable_type != $type): ?> style="display:none"<?php endif; ?>>
			<colgroup>
				<col class="key" />
				<col class="val" />
			</colgroup>
			<thead>
				<tr>
					<th colspan="2"><?=lang('settings_for')?> <?=$row['name']?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($row['settings'] AS $cells): ?>
					<tr class="<?=low_zebra()?>">
						<?php if (count($cells) == 1): ?>
							<td colspan="2"><?=$cells[0]?></td>
						<?php else: ?>
							<td><?=(string)@$cells[0]?></td>
							<td><?=(string)@$cells[1]?></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach; ?>

	<?php if ($variable_id == 'new'): ?>
		<table cellpadding="0" cellspacing="0" class="mainTable">
			<colgroup>
				<col class="key" />
				<col class="val" />
			</colgroup>
			<thead>
				<tr>
					<th colspan="2"><?=lang('creation_options')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="<?=low_zebra(TRUE)?>">
					<td style="vertical-align:top">
						<label class="low-label" for="low_variable_data"><?=lang('variable_data')?></label>
						<div class="low-var-notes"><?=lang('variable_data_help')?></div>
					</td>
					<td>
						<textarea name="variable_data" id="low_variable_data" rows="4" cols="40"></textarea>
					</td>
				</tr>
				<tr class="<?=low_zebra()?>">
					<td>
						<label class="low-label" for="low_variable_suffix"><?=lang('variable_suffix')?></label>
						<div class="low-var-notes"><?=lang('variable_suffix_help')?></div>
					</td>
					<td>
						<input type="text" name="variable_suffix" id="low_variable_suffix" class="medium" value="" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<button type="submit" class="submit"><?=lang('low_variables_save')?></button>

</form>