<tr>
	<td><?=lang('form:placeholder')?></td>
	<td>
		<?=form_input($form_name_settings.'[placeholder]', ((isset($placeholder) == TRUE) ? $placeholder : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:use_user_email')?> <span class="pophelp" data-key="use_user_email" title="<?=lang('form:use_user_email')?>"></span></td>
	<td class="ShowHideSubmitBtn">
		<?=form_radio($form_name_settings.'[use_member_email]', 'yes', ((isset($use_member_email) == FALSE OR $use_member_email == 'yes') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="useryes" ')?> <?=lang('form:yes')?>
		<?=form_radio($form_name_settings.'[use_member_email]', 'no', ((isset($use_member_email) == TRUE && $use_member_email == 'no') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="userno" ')?> <?=lang('form:no')?>

		<p class="btn_useryes">
			<br />
			<label><?=lang('form:hide_if_member_email')?></label> <br />
			<?=form_radio($form_name_settings.'[hide_if_member_email]', 'yes', ((isset($hide_if_member_email) == TRUE && $hide_if_member_email == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
			<?=form_radio($form_name_settings.'[hide_if_member_email]', 'no', ((isset($hide_if_member_email) == FALSE OR $hide_if_member_email == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		</p>
	</td>
</tr>

<tr>
	<td><?=lang('form:master_email')?> <span class="pophelp" data-key="master_email" title="<?=lang('form:master_email')?>"></span></td>
	<td class="ShowHideSubmitBtn">
		<?=form_radio($form_name_settings.'[master_email]', 'yes', ((isset($master_email) == FALSE OR $master_email == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
		<?=form_radio($form_name_settings.'[master_email]', 'no', ((isset($master_email) == TRUE && $master_email == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
	</td>
</tr>

<tr>
	<td><?=lang('form:add_email_to')?> </td>
	<td>
		<?=form_checkbox($form_name_settings.'[add_email_to][]', 'user_email_cc', ((in_array('user_email_cc', $add_email_to)) ? TRUE : FALSE))?> <?=lang('form:user_template_cc')?><br>
		<?=form_checkbox($form_name_settings.'[add_email_to][]', 'user_email_bcc', ((in_array('user_email_bcc', $add_email_to)) ? TRUE : FALSE))?> <?=lang('form:user_template_bcc')?><br>
		<?=form_checkbox($form_name_settings.'[add_email_to][]', 'admin_email_cc', ((in_array('admin_email_cc', $add_email_to)) ? TRUE : FALSE))?> <?=lang('form:admin_template_cc')?><br>
		<?=form_checkbox($form_name_settings.'[add_email_to][]', 'admin_email_bcc', ((in_array('admin_email_bcc', $add_email_to)) ? TRUE : FALSE))?> <?=lang('form:admin_template_bcc')?><br>
	</td>
</tr>
