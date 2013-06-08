<tr>
	<td><?=lang('form:fboard')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[board]', $fboards, $board)?>
	</td>
</tr>
<tr>
	<td><?=lang('form:limit_category')?></td>
	<td>
	<div style="width:300px">
		<?=form_multiselect($form_name_settings.'[categories][]', $fcats, $categories, "class='multiselect' id='{$form_name_settings}[categories]'")?>
		</div>
	</td>
</tr>
<tr>
	<td><?=lang('form:group_by_category')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[grouped]', array('no' => lang('form:no'), 'yes' => lang('form:yes')), $grouped)?>
	</td>
</tr>
<tr>
	<td><?=lang('form:what2store')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[store]', array('forum_name' => lang('form:forum_name'), 'forum_id' => lang('form:forum_id') ), $store)?>
	</td>
</tr>


