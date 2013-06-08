<tr>
	<td><?=lang('f:fieldtype')?></td>
	<td>
		<?=form_radio($form_name_settings.'[field_type]', 'input', ((isset($field_type) == FALSE OR $field_type == 'input') ? TRUE : FALSE), '  ')?> <?=lang('f:input_field')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'select', ((isset($field_type) == TRUE && $field_type == 'select') ? TRUE : FALSE), '  ')?> <?=lang('f:dropdown')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'hidden', ((isset($field_type) == TRUE && $field_type == 'hidden') ? TRUE : FALSE), '  ')?> <?=lang('f:hidden')?>
	</td>
</tr>

<tr>
	<td><?=lang('f:default_val')?></td>
	<td><?=form_input($form_name_settings.'[default_val]', $default_val)?></td>
</tr>

<tr>
	<td><?=lang('f:min_val')?></td>
	<td><?=form_input($form_name_settings.'[min]', $min)?></td>
</tr>

<tr>
	<td><?=lang('f:max_val')?></td>
	<td><?=form_input($form_name_settings.'[max]', $max)?></td>
</tr>
