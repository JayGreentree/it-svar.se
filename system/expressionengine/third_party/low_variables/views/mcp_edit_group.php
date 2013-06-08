<?php if ($errors): ?>
	<div class="low-alertbox">
		<ul><?php foreach($errors AS $msg): ?>
			<li><?=$msg?></li>
		<?php endforeach; ?></ul>
	</div>
<?php endif; ?>

<form method="post" action="<?=$base_url?>&amp;method=save_group" id="low-variable-form">
	<div>
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />
		<input type="hidden" name="group_id" value="<?=$group_id?>" />
		<input type="hidden" name="from" value="<?=$from?>" />
	</div>
	<table cellpadding="0" cellspacing="0" class="mainTable">
		<colgroup>
			<col class="key" />
			<col class="val" />
		</colgroup>
		<thead>
			<tr>
				<th colspan="2"><?=lang('edit_group')?> (#<?=$group_id?>)</th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?=low_zebra()?>">
				<td>
					<label class="low-label" for="group_label"><span class="alert">*</span> <?=lang('group_label')?></label>
				</td>
				<td>
					<?php if ($group_id): ?>
						<input type="text" name="group_label" id="low_group_label" class="medium" value="<?=htmlspecialchars($group_label)?>" />
						<?php if ($group_id == 'new'): ?><script type="text/javascript"> document.getElementById('low_group_label').focus(); </script><?php endif; ?>
					<?php else: ?>
						<?=htmlspecialchars($group_label)?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if ($group_id): ?>
				<tr class="<?=low_zebra()?>">
					<td style="vertical-align:top">
						<label class="low-label" for="group_notes"><?=lang('group_notes')?></label>
						<div class="low-var-notes"><?=lang('group_notes_help')?></div>
					</td>
					<td>
						<textarea name="group_notes" id="group_notes" rows="4" cols="40"><?=htmlspecialchars($group_notes)?></textarea>
					</td>
				</tr>
			<?php endif; ?>
			<?php if ($group_id != 'new'): ?>
				<?php if ($variables): ?>
					<tr class="<?=low_zebra()?>">
						<td style="vertical-align:top">
							<span class="low-label"><?=lang('variable_order')?></span>
						</td>
						<td style="padding:0">
							<ul id="low-variables-list">
								<?php foreach($variables AS $i => $row): ?>
									<li>
										<input type="hidden" name="vars[]" value="<?=$row['variable_id']?>" />
										<?=(strlen($row['variable_label'])?$row['variable_label']:$row['variable_name'])?>
									</li>
								<?php endforeach; ?>
							</ul>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ($group_id): ?>
					<tr class="<?=low_zebra()?>">
						<td colspan="2">
							<label style="display:block">
								<input type="checkbox" name="save_as_new_group" value="y" />
								<?=lang('save_as_new_group_label')?>
							</label>
						</td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<?php if ($group_id && $group_id != 'new'): ?>
		<div id="new-group-options" style="display:none">
			<table cellpadding="0" cellspacing="0" class="mainTable">
				<colgroup>
					<col class="key" />
					<col class="val" />
				</colgroup>
				<thead>
					<tr>
						<th colspan="2"><?=lang('new_group_options')?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="<?=low_zebra(TRUE)?>">
						<td>
							<strong class="low-label"><?=lang('duplicate_variables')?></strong>
						</td>
						<td>
							<label>
								<input type="checkbox" name="duplicate_variables" value="y" />
								<?=lang('duplicate_variables_label')?>
							</label>
						</td>
					</tr>
					<tr class="<?=low_zebra()?>">
						<td>
							<label class="low-label" for="low_variable_suffix"><?=lang('variable_suffix')?></label>
							<div class="low-var-notes"><?=lang('group_variable_suffix_help')?></div>
						</td>
						<td>
							<input type="text" name="variable_suffix" id="low_variable_suffix" class="medium" value="" />
						</td>
					</tr>
					<tr class="<?=low_zebra()?>">
						<td>
							<label for="with_suffix" class="low-label"><?=lang('suffix_options')?></label>
						</td>
						<td>
							<select name="with_suffix" id="with_suffix">
								<option value="append"><?=lang('append_suffix')?></option>
								<option value="replace"><?=lang('replace_suffix')?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<button type="submit" class="submit"><?=lang('low_variables_save')?></button>

</form>