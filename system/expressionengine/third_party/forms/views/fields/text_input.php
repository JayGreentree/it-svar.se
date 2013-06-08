<tr>
	<td><?=lang('form:placeholder')?></td>
	<td>
		<?=form_input($form_name_settings.'[placeholder]', ((isset($placeholder) == TRUE) ? $placeholder : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:max_chars')?> <span class="pophelp" data-key="max_chars" title="<?=lang('form:max_chars')?>"></span></td>
	<td>
		<?=form_input($form_name_settings.'[max_chars]', ((isset($max_chars) == TRUE) ? $max_chars : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:default_val')?> <span class="pophelp" data-key="default_val" title="<?=lang('form:default_val')?>"></span></td>
	<td>
		<?=form_input($form_name_settings.'[default_value]', ((isset($default_value) == TRUE) ? $default_value : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:password_field')?></td>
	<td>
		<?=form_radio($form_name_settings.'[password_field]', 'no', ((isset($password_field) == FALSE OR $password_field == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[password_field]', 'yes', ((isset($password_field) == TRUE && $password_field == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>
