<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="Templates" style="padding:20px;">
<?=form_open($base_url_short.AMP.'method=update_template')?>
<?=form_hidden('template_id', $template_id);?>

<div class="FormTable" style="float:left; width:48%; margin-right:1%;">
	<h2><?=lang('form:tmpl_gen_info')?></h2>
	<table>
		<tbody>
			<tr>
				<td class="flabel"><?=lang('form:type')?></td>
				<td>
					<?=form_radio('template_type', 'user', (($template_type == FALSE OR $template_type == 'user') ? TRUE : FALSE)  )?> <?=lang('form:tmpl:user')?> &nbsp;
					<?=form_radio('template_type', 'admin', (($template_type == 'admin') ? TRUE : FALSE)  )?> <?=lang('form:tmpl:admin')?>
				</td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl_label')?></td>
				<td><?=form_input('template_label', $template_label)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl_name')?></td>
				<td><?=form_input('template_name', $template_name)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:desc')?></td>
				<td><?=form_input('template_desc', $template_desc)?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class="FormTable" style="float:left; width:48%;">
	<h2><?=lang('form:tmpl_gen_info')?></h2>
	<table>
		<tbody>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:type')?></td>
				<td>
					<?=form_radio('email_type', 'text', (($email_type == FALSE OR $email_type == 'text') ? TRUE : FALSE)  )?> <?=lang('form:tmpl:email:text')?> &nbsp;
					<?=form_radio('email_type', 'html', (($email_type == 'html') ? TRUE : FALSE)  )?> <?=lang('form:tmpl:email:html')?>
				</td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:wordwrap')?></td>
				<td>
					<?=form_radio('email_wordwrap', 'yes', (($email_wordwrap == 'yes') ? TRUE : FALSE)  )?> <?=lang('form:yes')?>
					<?=form_radio('email_wordwrap', 'no', (($email_wordwrap == FALSE OR $email_wordwrap == 'no') ? TRUE : FALSE)  )?> <?=lang('form:no')?> &nbsp;
				</td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:send_attach')?></td>
				<td>
					<?=form_radio('email_attachments', 'yes', (($email_attachments == FALSE OR $email_attachments == 'yes') ? TRUE : FALSE)  )?> <?=lang('form:yes')?>
					<?=form_radio('email_attachments', 'no', (($email_attachments == 'no') ? TRUE : FALSE)  )?> <?=lang('form:no')?> &nbsp;
				</td>
			</tr>
			<tr>
				<td><label></label></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>

<br clear="all">

<div class="FormTable" style="float:left; width:48%; margin-right:1%;">
	<h2><?=lang('form:tmpl_email_info')?></h2>
	<table>
		<tbody>
			<tr class="admin_only">
				<td class="flabel"><?=lang('form:tmpl:email:to')?></td>
				<td><?=form_input('email_to', $email_to)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:from')?></td>
				<td><?=form_input('email_from', $email_from)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:from_email')?></td>
				<td><?=form_input('email_from_email', $email_from_email)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:subject')?></td>
				<td><?=form_input('email_subject', $email_subject)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:cc')?></td>
				<td><?=form_input('email_cc', $email_cc)?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class="FormTable" style="float:left; width:48%;">
	<h2><?=lang('form:tmpl_email_info')?></h2>
	<table>
		<tbody>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:reply_to')?></td>
				<td><?=form_input('email_reply_to', $email_reply_to)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:reply_to_email')?></td>
				<td><?=form_input('email_reply_to_email', $email_reply_to_email)?></td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:reply_to_author')?></td>
				<td>
					<?=form_radio('reply_to_author', 'yes', (($reply_to_author == FALSE OR $reply_to_author == 'yes') ? TRUE : FALSE)  )?> <?=lang('form:yes')?>
					<?=form_radio('reply_to_author', 'no', (($reply_to_author == 'no') ? TRUE : FALSE)  )?> <?=lang('form:no')?> &nbsp;
				</td>
			</tr>
			<tr>
				<td class="flabel"><?=lang('form:tmpl:email:bcc')?></td>
				<td><?=form_input('email_bcc', $email_bcc)?></td>
			</tr>
		</tbody>
	</table>
</div>

<br clear="all">

<div class="FormTable">
	<h2><?=lang('form:tmpl:email:template')?></h2>
	<table>
		<tbody>
			<tr>
				<td style="width:60%"><textarea name="template" rows="15"><?=$template?></textarea></td>
				<td><?=lang('form:email_template_exp')?></td>
			</tr>
		</tbody>
	</table>
</div>


<input class="submit" type="submit" value="<?=lang('form:save')?>">



<?=form_close()?>
</div><!--fbody-->

<br clear="all">
<?php echo $this->view('mcp/_footer'); ?>