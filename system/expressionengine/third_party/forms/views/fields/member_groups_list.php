<tr>
	<td><?=lang('form:what2store')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[store]', array('group_title' => lang('form:group_name'), 'group_id' => lang('form:group_id')), $store)?>
	</td>
</tr>