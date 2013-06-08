<tr>
	<td><?=lang('form:hide_country')?></td>
	<td>
		<?=form_radio($form_name_settings.'[hide_country]', 'no', ((isset($hide_country) == FALSE OR $hide_country == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[hide_country]', 'yes', ((isset($hide_country) == TRUE && $hide_country == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:hide_state')?></td>
	<td>
		<?=form_radio($form_name_settings.'[hide_state]', 'no', ((isset($hide_state) == FALSE OR $hide_state == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[hide_state]', 'yes', ((isset($hide_state) == TRUE && $hide_state == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:hide_address2')?></td>
	<td>
		<?=form_radio($form_name_settings.'[hide_address2]', 'no', ((isset($hide_address2) == FALSE OR $hide_address2 == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[hide_address2]', 'yes', ((isset($hide_address2) == TRUE && $hide_address2 == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:hide_zip')?></td>
	<td>
		<?=form_radio($form_name_settings.'[hide_zip]', 'no', ((isset($hide_zip) == FALSE OR $hide_zip == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[hide_zip]', 'yes', ((isset($hide_zip) == TRUE && $hide_zip == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('f:default_country')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[default_country]', $countries, ((isset($default_country) == TRUE) ? $default_country : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('f:master_for')?></td>
	<td>
		<?=form_checkbox($form_name_settings.'[master_for][]', 'mailinglist', ((isset($master_for) == TRUE && in_array('mailinglist', $master_for) == TRUE) ? TRUE : FALSE))?> <?=lang('f:mailinglist')?>&nbsp;&nbsp;
		<?=form_checkbox($form_name_settings.'[master_for][]', 'billing', ((isset($master_for) == TRUE && in_array('billing', $master_for) == TRUE) ? TRUE : FALSE))?> <?=lang('f:billing')?>&nbsp;&nbsp;
		<?=form_checkbox($form_name_settings.'[master_for][]', 'shipping', ((isset($master_for) == TRUE && in_array('shipping', $master_for) == TRUE) ? TRUE : FALSE))?> <?=lang('f:shipping')?>
	</td>
</tr>
