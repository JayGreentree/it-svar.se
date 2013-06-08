<tr>
	<td><?=lang('form:default_val')?> <span class="pophelp" data-key="default_val" title="<?=lang('form:default_val')?>"></span></td>
	<td>
		<?=form_input($form_name_settings.'[default_value]', ((isset($default_value) == TRUE) ? $default_value : ''))?>
	</td>
</tr>