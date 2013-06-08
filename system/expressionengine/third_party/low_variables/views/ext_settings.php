<form method="post" action="<?=BASE?>&amp;D=cp&amp;C=addons_extensions&amp;M=save_extension_settings">
	<div>
		<input type="hidden" name="file" value="<?=(LOW_VAR_PACKAGE)?>" />
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />
	</div>
	<table cellpadding="0" cellspacing="0" style="width:100%" class="mainTable">
		<colgroup>
			<col style="width:50%" />
			<col style="width:50%" />
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><?=lang('preference')?></th>
				<th scope="col"><?=lang('setting')?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="license_key"><span class="alert">*</span> <?=lang('license_key')?></label>
					<div class="low-var-notes"><?=lang('license_key_help')?></div>
				</td>
				<td>
					<input type="text" name="license_key" id="license_key" style="width:90%" value="<?=htmlspecialchars($license_key)?>" />
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td style="vertical-align:top">
					<strong class="low-label"><?=lang('can_manage')?></strong>
					<div class="low-var-notes"><?=lang('can_manage_help')?></div>
				</td>
				<td>
					<?php foreach ($member_groups AS $group_id => $group_name): ?>
						<label style="display:block;cursor:pointer">
							<input type="checkbox" name="can_manage[]" value="<?=$group_id?>"
							<?php if (in_array($group_id, $can_manage)): ?>checked="checked" <?php endif; ?>
							/> <?=htmlspecialchars($group_name)?>
						</label>
					<?php endforeach; ?>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<label for="clear_cache" class="low-label"><?=lang('clear_cache')?></label>
					<div class="low-var-notes"><?=lang('clear_cache_help')?></div>
				</td>
				<td>
					<select name="clear_cache" id="clear_cache">
					<?php foreach ($clear_cache_options AS $key => $val): ?>
						<option value="<?=$key?>"<?php if ($key == $clear_cache): ?> selected="selected"<?php endif; ?>>
							<?=$val?>
						</option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label"><?=lang('register_globals')?></label>
					<div class="low-var-notes"><?=lang('register_globals_help')?></div>
				</td>
				<td>
					<select name="register_globals" id="register_globals">
					<?php foreach ($register_globals_options AS $key => $val): ?>
						<option value="<?=$key?>"<?php if ($key == $register_globals): ?> selected="selected"<?php endif; ?>>
							<?=$val?>
						</option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<strong class="low-label"><?=lang('register_member_data')?></strong>
					<div class="low-var-notes"><?=lang('register_member_data_help')?></div>
				</td>
				<td>
					<label style="cursor:pointer">
						<input type="radio" name="register_member_data" value="y"
						<?php if ($register_member_data == 'y'): ?> checked="checked"<?php endif; ?>
						/> <?=lang('yes')?>
					</label>
					<label style="cursor:pointer;margin-left:10px">
						<input type="radio" name="register_member_data" value="n"
						<?php if ($register_member_data == 'n'): ?> checked="checked"<?php endif; ?>
						/> <?=lang('no')?>
					</label>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<strong class="low-label"><?=lang('save_as_files')?></strong>
					<div class="low-var-notes"><?=lang('save_as_files_help')?></div>
				</td>
				<td>
					<label style="cursor:pointer">
						<input type="radio" name="save_as_files" value="y"
						<?php if ($save_as_files == 'y'): ?> checked="checked"<?php endif; ?>
						<?php if ($cfg['save_as_files'] !== FALSE): ?> disabled="disabled"<?php endif; ?>
						/> <?=lang('yes')?>
					</label>
					<label style="cursor:pointer;margin-left:10px">
						<input type="radio" name="save_as_files" value="n"
						<?php if ($save_as_files == 'n'): ?> checked="checked"<?php endif; ?>
						<?php if ($cfg['save_as_files'] !== FALSE): ?> disabled="disabled"<?php endif; ?>
						/> <?=lang('no')?>
					</label>
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="file_path"><?=lang('file_path')?></label>
					<div class="low-var-notes"><?=lang('file_path_help')?></div>
				</td>
				<td>
					<input type="text" name="file_path" id="file_path" style="width:90%" value="<?=htmlspecialchars($file_path)?>"
					<?php if ($cfg['file_path'] !== FALSE): ?>disabled="disabled"<?php endif; ?> />
				</td>
			</tr>
			<tr class="<?=low_zebra()?>">
				<td style="vertical-align:top">
					<strong class="low-label"><?=lang('variable_types')?></strong>
					<div class="low-var-notes"><?=lang('variable_types_help')?></div>
				</td>
				<td>
					<div class="low-cell-header">
						<label>
							<input type="checkbox" id="low-select-all-types" />
							<?=lang('select_all')?>
						</label>
					</div>

					<?php foreach($variable_types AS $type => $info): ?>
						<label style="cursor:pointer">
							<input type="checkbox" name="enabled_types[]" value="<?=$type?>"
							<?php if(in_array($type, $enabled_types)): ?>checked="checked" <?php endif; ?>
							<?php if($info['is_default']): ?> disabled="disabled"<?php endif; ?>
							/> <?=$info['name']?> &ndash; <small><?=$info['version']?></small>
						</label><br />
					<?php endforeach; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="submit" class="submit" value="<?=lang('submit')?>" />
</form>