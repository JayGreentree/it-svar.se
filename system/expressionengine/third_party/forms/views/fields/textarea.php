<tr>
	<td><?=lang('form:placeholder')?></td>
	<td>
		<?=form_input($form_name_settings.'[placeholder]', ((isset($placeholder) == TRUE) ? $placeholder : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:rows')?></td>
	<td>
		<?=form_input($form_name_settings.'[rows]', ((isset($rows) == TRUE) ? $rows : '4'))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:cols')?></td>
	<td>
		<?=form_input($form_name_settings.'[cols]', ((isset($cols) == TRUE) ? $cols : '50'))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:disabled')?></td>
	<td>
		<?=form_radio($form_name_settings.'[disabled]', 'no', ((isset($disabled) == FALSE OR $disabled == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[disabled]', 'yes', ((isset($disabled) == TRUE && $disabled == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:default_text')?></td>
	<td>
		<?=form_textarea($form_name_settings.'[default_text]', ((isset($default_text) == TRUE) ? $default_text : ''))?>
	</td>
</tr>