<tr>
	<td><?=lang('form:limit_mgroups')?></td>
	<td>
	<div style="width:300px">
		<?=form_multiselect($form_name_settings.'[member_groups][]', $mgroups, $member_groups, "class='multiselect' ")?>
		</div>
	</td>
</tr>
<tr>
	<td><?=lang('form:group_by_mem_group')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[grouped]', array('no' => lang('form:no'), 'yes' => lang('form:yes')), $grouped)?>
	</td>
</tr>
<tr>
	<td><?=lang('form:what2store')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[store]', array('screen_name' => lang('form:screen_name'), 'username' => lang('form:username'), 'email' => lang('form:email'), 'member_id' => lang('form:member_id')), $store)?>
	</td>
</tr>