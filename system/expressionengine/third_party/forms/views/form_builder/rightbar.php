<?php if ($field_id == 0):?>
<h6><?=lang('form:general')?></h6>
<div class="section">
<label><?=lang('form:form_name')?></label>
<?=form_input($field_name.'[settings][form_title]', ($form['form_title'] ? $form['form_title'] : 'Untitled'), ' id="form_title" ')?>
<label><?=lang('form:form_url_title')?></label>
<?=form_input($field_name.'[settings][form_url_title]', ($form['form_url_title'] ? $form['form_url_title'] : 'untitled'), ' id="form_url_title" ')?>
</div>
<?php endif;?>

<h6 style="margin:0"><?=lang('form:form_settings')?></h6>
<div class="settings_toggler">
	<span class="label label-info" data-section="email_admin"><?=lang('form:email_admin')?></span>
	<span class="label label-info" data-section="email_user"><?=lang('form:email_user')?></span>
	<span class="label label-info" data-section="field_labels"><?=lang('form:field_labels')?></span>
	<span class="label label-info" data-section="submit_button"><?=lang('form:submit_button')?></span>
	<span class="label label-info" data-section="restrictions"><?=lang('form:restrictions')?></span>
	<span class="label label-info" data-section="submit_flow"><?=lang('form:submit_flow')?></span>
	<span class="label label-info" data-section="security"><?=lang('form:security')?></span>
	<span class="label label-info" data-section="3rdparty_submission"><?=lang('form:3rdparty_submission')?></span>
	<span class="label label-info" data-section="cp_dashboard"><?=lang('f:cp_dashboard')?></span>

	<div class="form_settings email_admin" style="width:800px">
		<div class="FormTable">
		<h2><?=lang('form:email_admin')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody class="template_toggler">
				<tr>
					<td class="flabel"><?=lang('form:tmpl')?></td>
					<td>
						<?=form_radio($field_name.'[templates][admin][which]', 'predefined', (($form['admin_template'] > 0) ? TRUE : FALSE))?> <?=lang('form:tmpl:predefined')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[templates][admin][which]', 'custom', (($form['admin_template'] == -1) ? TRUE : FALSE))?> <?=lang('form:tmpl:custom')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[templates][admin][which]', 'none', (($form['admin_template'] == 0) ? TRUE : FALSE))?> <?=lang('form:tmpl:none')?>
					</td>
				</tr>
			</tbody>
			<tbody class="toggle predefined">
				<tr>
					<td class="flabel"><?=lang('form:tmpl_predefined')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][admin][predefined]', $email_templates['admin'], $form['admin_template'])?>
					</td>
				</tr>
			</tbody>
			<tbody class="toggle custom">
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:type')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][admin][custom][email_type]', $config['email_types'], $form['templates']['admin']['email_type'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:wordwrap')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][admin][custom][email_wordwrap]', $config['yes_no'], $form['templates']['admin']['email_wordwrap'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:to')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_to]', $form['templates']['admin']['email_to'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:from')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_from]', $form['templates']['admin']['email_from'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:from_email')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_from_email]', $form['templates']['admin']['email_from_email'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:reply_to')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_reply_to]', $form['templates']['admin']['email_reply_to'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:reply_to_email')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_reply_to_email]', $form['templates']['admin']['email_reply_to_email'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:reply_to_author')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][admin][custom][reply_to_author]', array_reverse($config['yes_no'], TRUE), $form['templates']['admin']['reply_to_author'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:subject')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_subject]', $form['templates']['admin']['email_subject'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:cc')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_cc]', $form['templates']['admin']['email_cc'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:bcc')?></td>
					<td>
						<?=form_input($field_name.'[templates][admin][custom][email_bcc]', $form['templates']['admin']['email_bcc'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:send_attach')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][admin][custom][email_attachments]', $config['yes_no'], $form['templates']['admin']['email_attachments'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:template')?></td>
					<td>
						<?=form_textarea($field_name.'[templates][admin][custom][template]', $form['templates']['admin']['template'], 'rows="15"')?>
						<?=lang('form:email_template_exp')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings email_user" style="width:800px">
		<div class="FormTable">
		<h2><?=lang('form:email_user')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody class="template_toggler">
				<tr>
					<td class="flabel"><?=lang('form:tmpl')?></td>
					<td>
						<?=form_radio($field_name.'[templates][user][which]', 'predefined', (($form['user_template'] > 0) ? TRUE : FALSE))?> <?=lang('form:tmpl:predefined')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[templates][user][which]', 'custom', (($form['user_template'] == -1) ? TRUE : FALSE))?> <?=lang('form:tmpl:custom')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[templates][user][which]', 'none', (($form['user_template'] == 0) ? TRUE : FALSE))?> <?=lang('form:tmpl:none')?>
					</td>
				</tr>
			</tbody>
			<tbody class="toggle predefined">
				<tr>
					<td class="flabel"><?=lang('form:tmpl_predefined')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][user][predefined]', $email_templates['user'], $form['user_template'])?>
					</td>
				</tr>
			</tbody>
			<tbody class="toggle custom">
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:type')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][user][custom][email_type]', $config['email_types'], $form['templates']['user']['email_type'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:wordwrap')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][user][custom][email_wordwrap]', $config['yes_no'], $form['templates']['user']['email_wordwrap'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:from')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_from]', $form['templates']['user']['email_from'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:from_email')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_from_email]', $form['templates']['user']['email_from_email'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:reply_to')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_reply_to]', $form['templates']['user']['email_reply_to'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:reply_to_email')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_reply_to_email]', $form['templates']['user']['email_reply_to_email'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:subject')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_subject]', $form['templates']['user']['email_subject'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:cc')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_cc]', $form['templates']['user']['email_cc'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:bcc')?></td>
					<td>
						<?=form_input($field_name.'[templates][user][custom][email_bcc]', $form['templates']['user']['email_bcc'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:send_attach')?></td>
					<td>
						<?=form_dropdown($field_name.'[templates][user][custom][email_attachments]', $config['yes_no'], $form['templates']['user']['email_attachments'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:tmpl:email:template')?></td>
					<td>
						<?=form_textarea($field_name.'[templates][user][custom][template]', $form['templates']['user']['template'], 'rows="15"')?>
						<?=lang('form:email_template_exp')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings field_labels">
		<div class="FormTable">
		<h2><?=lang('form:field_labels')?> <a class="abtn backr" href="#" data-refreshfields="yes"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr class="label_placement">
					<td class="flabel"><?=lang('form:label_placement')?> <span class="pophelp" data-key="label_placement" title="<?=lang('form:label_placement')?>"></span></td>
					<td>
						<?=form_radio($field_name.'[settings][label_placement]', 'top', (($form['settings']['label_placement'] == 'top') ? TRUE : FALSE))?> <?=lang('form:place:top')?> <br>
						<?=form_radio($field_name.'[settings][label_placement]', 'left_align', (($form['settings']['label_placement'] == 'left_align') ? TRUE : FALSE))?> <?=lang('form:place:left_align')?> <br>
						<?=form_radio($field_name.'[settings][label_placement]', 'right_align', (($form['settings']['label_placement'] == 'right_align') ? TRUE : FALSE))?> <?=lang('form:place:right_align')?> <br>
						<?=form_radio($field_name.'[settings][label_placement]', 'bottom', (($form['settings']['label_placement'] == 'bottom') ? TRUE : FALSE))?> <?=lang('form:place:bottom')?> <br>
						<?=form_radio($field_name.'[settings][label_placement]', 'none', (($form['settings']['label_placement'] == 'none') ? TRUE : FALSE))?> <?=lang('form:place:none')?>
					</td>
				</tr>
				<tr  class="label_placement">
					<td class="flabel"><?=lang('form:desc_placement')?> <span class="pophelp" data-key="desc_placement" title="<?=lang('form:desc_placement')?>"></span></td>
					<td>
						<?=form_radio($field_name.'[settings][desc_placement]', 'bottom', (($form['settings']['desc_placement'] == 'bottom') ? TRUE : FALSE))?> <?=lang('form:place:bottom')?> <br>
						<?=form_radio($field_name.'[settings][desc_placement]', 'top', (($form['settings']['desc_placement'] == 'top') ? TRUE : FALSE))?> <?=lang('form:place:top')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings submit_button">
		<div class="FormTable">
		<h2><?=lang('form:submit_button')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr>
					<td class="flabel"><?=lang('form:submit_button')?></td>
					<td>
						<?=form_radio($field_name.'[settings][submit_button][type]', 'default', (($form['settings']['submit_button']['type'] == 'default') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="default" ')?> <?=lang('form:button:default')?>
						<?=form_radio($field_name.'[settings][submit_button][type]', 'image', (($form['settings']['submit_button']['type'] == 'image') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="image" ')?> <?=lang('form:button:image')?>
						<br />
						<p class="btn_default"><?=lang('form:button:btext')?> <?=form_input($field_name.'[settings][submit_button][text]', $form['settings']['submit_button']['text'], 'style="width:50%"')?></p>
						<p class="btn_default"><?=lang('form:button:btext_next')?> <?=form_input($field_name.'[settings][submit_button][text_next_page]', $form['settings']['submit_button']['text_next_page'], 'style="width:50%"')?></p>
						<p class="btn_image"><?=lang('form:button:bimg')?> <?=form_input($field_name.'[settings][submit_button][img_url]', $form['settings']['submit_button']['img_url'], 'style="width:50%"')?></p>
						<p class="btn_image"><?=lang('form:button:bimg_next')?> <?=form_input($field_name.'[settings][submit_button][img_url_next_page]', $form['settings']['submit_button']['img_url_next_page'], 'style="width:50%"')?></p>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings restrictions">
		<div class="FormTable">
		<h2><?=lang('form:restrictions')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr>
					<td class="flabel"><?=lang('form:form_enabled')?></td>
					<td>
						<?=form_dropdown($field_name.'[settings][form_enabled]', array_reverse($config['yes_no'], TRUE), $form['settings']['form_enabled'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:max_entries')?></td>
					<td>
						<?=form_input($field_name.'[settings][max_entries]', $form['settings']['max_entries'])?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:open_fromto')?></td>
					<td>
						<?=lang('form:from')?> <?=form_input($field_name.'[settings][open_fromto][from]', $form['settings']['open_fromto']['from'], 'style="width:150px" class="datepicker" ')?> <br>
						<?=lang('form:to')?> <?=form_input($field_name.'[settings][open_fromto][to]', $form['settings']['open_fromto']['to'], 'style="width:150px" class="datepicker" ')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:allow_mgroups')?></td>
					<td>
						<?=form_multiselect($field_name.'[settings][member_groups][]', $member_groups, $form['settings']['member_groups'], ' class="chosen" style="width:300px; height:50px;" ')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings submit_flow">
		<div class="FormTable">
		<h2><?=lang('form:post_submission')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr>
					<td class="flabel"><?=lang('form:return_url')?> <span class="pophelp" data-key="return_url" title="<?=lang('form:return_url')?>"></span></td>
					<td>
						<?=form_input($field_name.'[settings][return_url]', ($form['settings']['return_url'] ? $form['settings']['return_url'] : ''))?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:success_msg_when')?></td>
					<td>
						<?=form_radio($field_name.'[settings][confirmation][when]', 'before_redirect', (($form['settings']['confirmation']['when'] == 'before_redirect') ? TRUE : FALSE), '  ')?> <?=lang('form:success:before_redirect')?> <br>
						<?=form_radio($field_name.'[settings][confirmation][when]', 'after_redirect', (($form['settings']['confirmation']['when'] == 'after_redirect') ? TRUE : FALSE), ' ')?> <?=lang('form:success:after_redirect')?> <br>
						<?=form_radio($field_name.'[settings][confirmation][when]', 'show_only', (($form['settings']['confirmation']['when'] == 'show_only') ? TRUE : FALSE), ' ')?> <?=lang('form:success:show_only')?> <br>
						<?=form_radio($field_name.'[settings][confirmation][when]', 'disabled', (($form['settings']['confirmation']['when'] == 'disabled') ? TRUE : FALSE), ' ')?> <?=lang('form:success:disabled')?> &nbsp;
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:success_msg')?> <span class="pophelp" data-key="success_msg" title="<?=lang('form:success_msg')?>"></span></td>
					<td>
						<?=form_textarea($field_name.'[settings][confirmation][text]', ($form['settings']['confirmation']['text'] ? $form['settings']['confirmation']['text'] : 'Thanks for contacting us! We will get in touch with you shortly.'))?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:redir_timeout')?></td>
					<td>
						<?=form_input($field_name.'[settings][confirmation][redirect_timeout]', ($form['settings']['confirmation']['redirect_timeout'] ? $form['settings']['confirmation']['redirect_timeout'] : '1'), 'style="width:50px"')?> <?=lang('form:seconds')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:success_show_form')?></td>
					<td>
						<?=form_radio($field_name.'[settings][confirmation][show_form]', 'no', (($form['settings']['confirmation']['show_form'] == 'no') ? TRUE : FALSE), '  ')?> <?=lang('form:no')?>&nbsp;
						<?=form_radio($field_name.'[settings][confirmation][show_form]', 'yes', (($form['settings']['confirmation']['show_form'] == 'yes') ? TRUE : FALSE), ' ')?> <?=lang('form:yes')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('f:debug_mode')?> <span class="pophelp" data-key="debug_mode" title="<?=lang('f:debug_mode')?>"></span></td>
					<td>
						<?=form_radio($field_name.'[settings][debug_mode]', 'no', (($form['settings']['debug_mode'] == 'no') ? TRUE : FALSE), '  ')?> <?=lang('form:no')?>&nbsp;
						<?=form_radio($field_name.'[settings][debug_mode]', 'yes', (($form['settings']['debug_mode'] == 'yes') ? TRUE : FALSE), ' ')?> <?=lang('form:yes')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings security">
		<div class="FormTable">
		<h2><?=lang('form:security')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr>
					<td class="flabel"><?=lang('form:snaptcha')?> <span class="pophelp" data-key="snaptcha" title="<?=lang('form:snaptcha')?>"></span></td>
					<td>
						<?=form_radio($field_name.'[settings][snaptcha]', 'no', (($form['settings']['snaptcha'] == 'no') ? TRUE : FALSE), '  ')?> <?=lang('form:no')?>
						<?=form_radio($field_name.'[settings][snaptcha]', 'yes', (($form['settings']['snaptcha'] == 'yes') ? TRUE : FALSE), ' ')?> <?=lang('form:yes')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:force_https')?> </td>
					<td>
						<?=form_radio($field_name.'[settings][force_https]', 'no', (($form['settings']['force_https'] == 'no') ? TRUE : FALSE), '  ')?> <?=lang('form:no')?>
						<?=form_radio($field_name.'[settings][force_https]', 'yes', (($form['settings']['force_https'] == 'yes') ? TRUE : FALSE), ' ')?> <?=lang('form:yes')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings 3rdparty_submission">
		<div class="FormTable">
		<h2><?=lang('form:3rdparty_submission')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr>
					<td class="flabel"><?=lang('form:third_party_flow')?></td>
					<td>
						<?=form_radio($field_name.'[settings][third_party][flow]', 'disabled', (($form['settings']['third_party']['flow'] == 'disabled') ? TRUE : FALSE), '  ')?> <?=lang('form:disabled')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[settings][third_party][flow]', 'post_submit', (($form['settings']['third_party']['flow'] == 'post_submit') ? TRUE : FALSE), ' ')?> <?=lang('form:post_submit')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:third_party_url')?></td>
					<td>
						<?=form_input($field_name.'[settings][third_party][url]', ($form['settings']['third_party']['url'] ? $form['settings']['third_party']['url'] : ''))?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:field_identifier')?></td>
					<td>
						<?=form_radio($field_name.'[settings][third_party][field_identifier]', 'field_name', (($form['settings']['third_party']['field_identifier'] == 'field_name') ? TRUE : FALSE), '  ')?> <?=lang('form:field_name')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[settings][third_party][field_identifier]', 'field_id', (($form['settings']['third_party']['field_identifier'] == 'field_id') ? TRUE : FALSE), ' ')?> <?=lang('form:field_id')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('form:save_fentry')?></td>
					<td>
						<?=form_radio($field_name.'[settings][save_fentry]', 'yes', (($form['settings']['save_fentry'] == 'yes') ? TRUE : FALSE), '  ')?> <?=lang('form:yes')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[settings][save_fentry]', 'no', (($form['settings']['save_fentry'] == 'no') ? TRUE : FALSE), ' ')?> <?=lang('form:no')?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="form_settings cp_dashboard">
		<div class="FormTable">
		<h2><?=lang('f:cp_dashboard')?> <a class="abtn backr" href="#"><span><?=lang('form:back')?></span></a></h2>
		<table>
			<tbody>
				<tr>
					<td class="flabel"><?=lang('f:dashboard:show')?></td>
					<td>
						<?=form_radio($field_name.'[settings][cp_dashboard][show]', 'yes', (($form['settings']['cp_dashboard']['show'] == 'yes') ? TRUE : FALSE), '  ')?> <?=lang('form:yes')?>&nbsp;&nbsp;
						<?=form_radio($field_name.'[settings][cp_dashboard][show]', 'no', (($form['settings']['cp_dashboard']['show'] == 'no') ? TRUE : FALSE), ' ')?> <?=lang('form:no')?>
					</td>
				</tr>
				<tr>
					<td class="flabel"><?=lang('f:dashboard:group')?></td>
					<td>
						<?php foreach($member_groups as $group_id => $group_label):?>
						<?=form_checkbox($field_name.'[settings][cp_dashboard][member_groups][]', $group_id, (isset($form['settings']['cp_dashboard']['member_groups']) === TRUE && (in_array($group_id, $form['settings']['cp_dashboard']['member_groups'])) ? TRUE : FALSE), '  ')?> <?=$group_label?> <br>
						<?php endforeach;?>
						<?=form_checkbox($field_name.'[settings][cp_dashboard][member_groups][]', 1, (isset($form['settings']['cp_dashboard']['member_groups']) === TRUE && (in_array(1, $form['settings']['cp_dashboard']['member_groups'])) ? TRUE : FALSE), '  ')?> Super Admins
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>


<?=form_hidden($field_name.'[field_name]', $field_name)?>
</div>
