<tr>
	<td><?=lang('form:date_input_type')?> <span class="pophelp" data-key="date_input_type" title="<?=lang('form:date_input_type')?>"></span></td>
	<td>
		<?=form_radio($form_name_settings.'[date_input_type]', 'datepicker', ((isset($date_input_type) == FALSE OR $date_input_type == 'datepicker') ? TRUE : FALSE))?> <?=lang('form:datepicker')?>
		<?=form_radio($form_name_settings.'[date_input_type]', 'dateselect', ((isset($date_input_type) == TRUE && $date_input_type == 'dateselect') ? TRUE : FALSE))?> <?=lang('form:dateselect')?>
		<?=form_radio($form_name_settings.'[date_input_type]', 'datefield', ((isset($date_input_type) == TRUE && $date_input_type == 'datefield') ? TRUE : FALSE))?> <?=lang('form:datefield')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:date_format')?></td>
	<td>
		<?php
		$opts = array();
		$opts['d/m/Y'] = date('d/m/Y');
		$opts['m/d/Y'] = date('m/d/Y');
		$opts['d-m-Y'] = date('d-m-Y');
		$opts['m-d-Y'] = date('m-d-Y');
		$opts['d/M/Y'] = date('d/M/Y');
		$opts['d/F/Y'] = date('d/F/Y');
		$opts['d-M-Y'] = date('d-M-Y');
		$opts['d-F-Y'] = date('d-F-Y');
		$opts['M d, Y'] = date('M d, Y');
		$opts['F d, Y'] = date('F d, Y');
		$opts['D F d, Y'] = date('D F d, Y');
		$opts['l F d, Y'] = date('l F d, Y');
		?>
		<?=form_dropdown($form_name_settings.'[date_format]', $opts, ((isset($date_format) != FALSE) ? $date_format : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:datepicker_format')?></td>
	<td>
		<?=form_radio($form_name_settings.'[datepicker_format]', 'american', ((isset($datepicker_format) == FALSE OR $datepicker_format == 'american') ? TRUE : FALSE))?> <?=lang('form:american')?> &nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[datepicker_format]', 'european', ((isset($datepicker_format) == TRUE && $datepicker_format == 'european') ? TRUE : FALSE))?> <?=lang('form:european')?>
	</td>
</tr>