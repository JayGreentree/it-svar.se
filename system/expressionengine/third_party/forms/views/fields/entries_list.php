<tr>
	<td><?=lang('form:limit_channel')?></td>
	<td>
	<div style="width:300px">
		<?=form_multiselect($form_name_settings.'[channels][]', $dbchannels, $channels, "class='multiselect' ")?>
		</div>
	</td>
</tr>
<tr>
	<td><?=lang('form:group_by_channel')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[grouped]', array('no' => lang('form:no'), 'yes' => lang('form:yes')), $grouped)?>
	</td>
</tr>
<tr>
	<td><?=lang('form:what2store')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[store]', array('entry_title' => lang('form:entry_title'), 'entry_url_title' => lang('form:entry_urltitle'), 'entry_id' =>  lang('form:entry_id')), $store)?>
	</td>
</tr>
<tr>
	<td><?=lang('form:form_element')?> </td>
	<td>
		<?=form_radio($form_name_settings.'[form_element]', 'select', ((isset($form_element) == FALSE || $form_element == 'select') ? TRUE : FALSE) )?> <?=lang('form:dropdown')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[form_element]', 'radio', ((isset($form_element) == TRUE && $form_element == 'radio') ? TRUE : FALSE) )?> <?=lang('form:radio')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[form_element]', 'checkbox', ((isset($form_element) == TRUE && $form_element == 'checkbox') ? TRUE : FALSE) )?> <?=lang('form:checkbox')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[form_element]', 'multiselect', ((isset($form_element) == TRUE && $form_element == 'multiselect') ? TRUE : FALSE) )?> <?=lang('form:multiselect')?>
	</td>
</tr>
<tr>
	<td><?=lang('form:order_by')?> </td>
	<td>
		<?=form_radio($form_name_settings.'[order_by]', 'title', ((isset($order_by) == FALSE || $order_by == 'title') ? TRUE : FALSE) )?> <?=lang('form:entry_title')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[order_by]', 'date', ((isset($order_by) == TRUE && $order_by == 'date') ? TRUE : FALSE) )?> <?=lang('form:entry_date')?>&nbsp;&nbsp;
	</td>
</tr>
<tr>
	<td><?=lang('form:sort')?> </td>
	<td>
		<?=form_radio($form_name_settings.'[sort]', 'asc', ((isset($sort) == FALSE || $sort == 'asc') ? TRUE : FALSE) )?> <?=lang('form:asc')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[sort]', 'desc', ((isset($sort) == TRUE && $sort == 'desc') ? TRUE : FALSE) )?> <?=lang('form:desc')?>&nbsp;&nbsp;
	</td>
</tr>
<tr>
	<td><?=lang('form:show_expired')?> </td>
	<td>
		<?=form_radio($form_name_settings.'[show_expired]', 'yes', ((isset($show_expired) == TRUE && $show_expired == 'yes') ? TRUE : FALSE) )?> <?=lang('form:yes')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[show_expired]', 'no', ((isset($show_expired) == FALSE || $show_expired == 'no') ? TRUE : FALSE) )?> <?=lang('form:no')?>&nbsp;&nbsp;
	</td>
</tr>
