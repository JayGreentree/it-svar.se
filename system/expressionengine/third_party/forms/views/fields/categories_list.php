<tr>
	<td><?=lang('form:limit_cat_groups')?></td>
	<td>
	<div style="width:300px">
		<?=form_multiselect($form_name_settings.'[cat_groups][]', $category_groups, $cat_groups, "class='multiselect' style='width:100%' ")?>
		</div>
	</td>
</tr>
<tr>
	<td><?=lang('form:group_by_cat_group')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[grouped]', array('no' => lang('form:no'), 'yes' => lang('form:yes')), $grouped)?>
	</td>
</tr>
<tr>
	<td><?=lang('form:what2store')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[store]', array('cat_name' => lang('form:cat_name'), 'cat_id' => lang('form:cat_id')), $store)?>
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

