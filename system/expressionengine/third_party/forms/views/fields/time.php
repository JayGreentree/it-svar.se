<tr>
	<td><?=lang('form:time_format')?></td>
	<td>
		<?=form_radio($form_name_settings.'[time_format]', '12h', ((isset($time_format) == FALSE OR $time_format == '12h') ? TRUE : FALSE))?> <?=lang('form:12h')?>
		<?=form_radio($form_name_settings.'[time_format]', '24h', ((isset($time_format) == TRUE && $time_format == '24h') ? TRUE : FALSE))?> <?=lang('form:24h')?>
	</td>
</tr>
