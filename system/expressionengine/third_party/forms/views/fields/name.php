<tr>
	<td style="width:50%;"><?=lang('form:show_prefix')?></td>
	<td>
		<?=form_radio($form_name_settings.'[show_prefix]', 'no', ((isset($show_prefix) == FALSE OR $show_prefix == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[show_prefix]', 'yes', ((isset($show_prefix) == TRUE && $show_prefix == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:show_suffix')?></td>
	<td>
		<?=form_radio($form_name_settings.'[show_suffix]', 'no', ((isset($show_suffix) == FALSE OR $show_suffix == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[show_suffix]', 'yes', ((isset($show_suffix) == TRUE && $show_suffix == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:prefix_select')?></td>
	<td>
		<?=form_radio($form_name_settings.'[prefix_select]', 'no', ((isset($prefix_select) == FALSE OR $prefix_select == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[prefix_select]', 'yes', ((isset($prefix_select) == TRUE && $prefix_select == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
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

