<tr>
	<td><?=lang('form:column_width')?> : 1</td>
	<td>
		<?=form_input($form_name_settings.'[column_width_1]', ((isset($column_width_1) == TRUE) ? $column_width_1 : '50%'))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:column_width')?> : 2</td>
	<td>
		<?=form_input($form_name_settings.'[column_width_2]', ((isset($column_width_2) == TRUE) ? $column_width_2 : '50%'))?>
	</td>
</tr>