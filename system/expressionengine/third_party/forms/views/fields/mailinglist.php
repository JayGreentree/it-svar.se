<tr>
	<td><?=lang('form:subscribe_text')?></td>
	<td>
		<?=form_input($form_name_settings.'[subscribe_text]', ((isset($subscribe_text) == TRUE) ? $subscribe_text : lang('form:subrtext')))?>
	</td>
</tr>

<tr>
	<td><?=lang('f:checked_default')?></td>
	<td>
		<?=form_radio($form_name_settings.'[checked]', 'no', ((isset($checked) == FALSE OR $checked == 'no') ? TRUE : FALSE), '  ')?> <?=lang('no')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[checked]', 'yes', ((isset($checked) == TRUE && $checked == 'yes') ? TRUE : FALSE), '  ')?> <?=lang('yes')?>&nbsp;&nbsp;
	</td>
</tr>

<tr>
	<td><?=lang('form:mailinglist_type')?></td>
	<td class="shide_triggers">
		<?php if (empty($ee['lists']) == FALSE):?>
		<?=form_radio($form_name_settings.'[type]', 'ee', ((isset($type) == FALSE OR $type== 'ee') ? TRUE : FALSE), '  ')?> <?=lang('form:mailinglist:ee')?>&nbsp;&nbsp;
		<?php endif;?>

		<?php if (empty($mailchimp['lists']) == FALSE):?>
		<?=form_radio($form_name_settings.'[type]', 'mailchimp', ((isset($type) == TRUE && $type== 'mailchimp') ? TRUE : FALSE), '  ')?> <?=lang('form:mailinglist:mailchimp')?>&nbsp;&nbsp;
		<?php endif;?>

		<?php if (empty($createsend['lists']) == FALSE):?>
		<?=form_radio($form_name_settings.'[type]', 'createsend', ((isset($type) == TRUE && $type== 'createsend') ? TRUE : FALSE), '  ')?> <?=lang('form:mailinglist:createsend')?>&nbsp;&nbsp;
		<?php endif;?>
	</td>
</tr>

<tr class="shide s-ee">
	<td><?=lang('form:mailinglist:list')?></td>
	<td><?=form_dropdown($form_name_settings.'[ee][list]', $ee['lists'], ((isset($ee['list']) != FALSE) ? $ee['list'] : ''))?></td>
</tr>

<tr class="shide s-mailchimp">
	<td><?=lang('form:mailinglist:list')?></td>
	<td><?=form_dropdown($form_name_settings.'[mailchimp][list]', $mailchimp['lists'], ((isset($mailchimp['list']) != FALSE) ? $mailchimp['list'] : ''))?></td>
</tr>

<tr class="shide s-createsend">
	<td><?=lang('form:mailinglist:list')?></td>
	<td><?=form_dropdown($form_name_settings.'[createsend][list]', $createsend['lists'], ((isset($createsend['list']) != FALSE) ? $createsend['list'] : ''))?></td>
</tr>
