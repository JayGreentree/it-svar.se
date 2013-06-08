<tr>
	<td><?=lang('form:placeholder')?></td>
	<td>
		<?=form_input($form_name_settings.'[placeholder]', ((isset($placeholder) == TRUE) ? $placeholder : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:range')?></td>
	<td>
		<?=lang('form:min')?> <?=form_input($form_name_settings.'[range_min]', ((isset($range_min) == TRUE) ? $range_min : ''), 'style="width:30px"')?> &nbsp;&nbsp;&nbsp;
		<?=lang('form:max')?> <?=form_input($form_name_settings.'[range_max]', ((isset($range_max) == TRUE) ? $range_max : ''), 'style="width:30px"')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:number_format')?></td>
	<td>
		<?=lang('form:thousands_sep')?> <span class="pophelp" data-key="thousands_sep" title="<?=lang('form:thousands_sep')?>"></span> <?=form_input($form_name_settings.'[thousands_sep]', ((isset($thousands_sep) == TRUE) ? $thousands_sep : ','), 'style="width:30px"')?> &nbsp;
		<!-- <?=lang('form:enforce')?> <span class="pophelp" data-key="enforce" title="<?=lang('form:enforce')?>"></span> <?=form_checkbox($form_name_settings.'[enforce_thousands_sep]', 'yes', ((isset($enforce_thousands_sep) == TRUE && $enforce_thousands_sep == 'yes') ? TRUE : FALSE));?> -->
		<br>
		<?=lang('form:dec_point')?> <span class="pophelp" data-key="dec_point" title="<?=lang('form:dec_point')?>"></span> <?=form_input($form_name_settings.'[dec_point]', ((isset($dec_point) == TRUE) ? $dec_point : '.'), 'style="width:30px"')?> &nbsp;
		<!-- <?=lang('form:enforce')?> <span class="pophelp" data-key="enforce" title="<?=lang('form:enforce')?>"></span> <?=form_checkbox($form_name_settings.'[enforce_dec_point]', 'yes', ((isset($enforce_dec_point) == TRUE && $enforce_dec_point == 'yes') ? TRUE : FALSE));?> -->
		<br>
		<?=lang('form:decimals')?> <span class="pophelp" data-key="decimals" title="<?=lang('form:decimals')?>"></span> <?=form_input($form_name_settings.'[decimals]', ((isset($decimals) == TRUE) ? $decimals : '2'), 'style="width:30px"')?> &nbsp;
		<!-- <?=lang('form:enforce')?> <span class="pophelp" data-key="enforce" title="<?=lang('form:enforce')?>"></span> <?=form_checkbox($form_name_settings.'[enforce_decimals]', 'yes', ((isset($enforce_decimals) == TRUE && $enforce_decimals == 'yes') ? TRUE : FALSE));?> -->
	</td>
</tr>
