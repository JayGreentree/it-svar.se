<tr>
	<td><?=lang('form:phone_cc')?></td>
	<td>
		<?=form_radio($form_name_settings.'[show_cc]', 'no', ((isset($show_cc) == FALSE OR $show_cc == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[show_cc]', 'yes', ((isset($show_cc) == TRUE && $show_cc == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:phone_area')?></td>
	<td>
		<?=form_radio($form_name_settings.'[show_area]', 'no', ((isset($show_area) == TRUE && $show_area == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[show_area]', 'yes', ((isset($show_area) == FALSE OR $show_area == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:phone_ext')?></td>
	<td>
		<?=form_radio($form_name_settings.'[show_ext]', 'no', ((isset($show_ext) == FALSE OR $show_ext == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[show_ext]', 'yes', ((isset($show_ext) == TRUE && $show_ext == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:phone_format')?> <span class="pophelp" data-key="phone_format" title="<?=lang('form:phone_format')?>"></span></td>
	<td>
		<?php
		$opts = array();
		$opts['usa'] = lang('form:tel_usa');
		$opts['int'] = lang('form:tel_int');
		?>
		<?=form_dropdown($form_name_settings.'[phone_format]', $opts, ((isset($phone_format) != FALSE) ? $phone_format : ''))?>
	</td>
</tr>
