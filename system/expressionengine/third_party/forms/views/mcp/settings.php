<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="Settings">
	<div class="btitle" id="actionbar">
		<h2><?=lang('form:settings')?></h2>
	</div>


<div style="padding:20px;">
<?=form_open($base_url_short.AMP.'method=update_settings')?>

	<div class="FormTable" style="width:49%; float:left;">
		<h2><?=lang('f:settings:general')?></h2>
		<table class="hasrowspan">
			<thead>
				<tr>
					<th><?=lang('f:settings:option')?></th>
					<th><?=lang('f:settings:value')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="odds">
					<td><?=lang('f:settings:dshbrd_on')?></td>
					<td>
						<?=form_radio('settings[cp_dashboard][enabled]', 'yes', (($config['cp_dashboard']['enabled'] == 'yes') ? TRUE : FALSE), '  ')?> <?=lang('form:yes')?>&nbsp;&nbsp;
						<?=form_radio('settings[cp_dashboard][enabled]', 'no', (($config['cp_dashboard']['enabled'] == 'no') ? TRUE : FALSE), ' ')?> <?=lang('form:no')?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="FormTable" style="width:49%; float:right;">
		<h2><?=lang('form:system_messages')?></h2>
		<table class="hasrowspan">
			<thead>
				<tr>
					<th><?=lang('form:ident')?></th>
					<th><?=lang('form:message')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="odds">
					<td>required_field</td>
					<td><?=form_input('settings[messages][required_field]', $config['messages']['required_field'])?></td>
				</tr>
				<tr class="evens">
					<td>form_submit_success_heading</td>
					<td><?=form_input('settings[messages][form_submit_success_heading]', $config['messages']['form_submit_success_heading'])?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<br clear="all">

	<div class="FormTable">
		<h2><?=lang('form:services_ext')?></h2>
		<table class="hasrowspan">
			<thead>
				<tr>
					<th><?=lang('form:service')?></th>
					<th colspan="2"><?=lang('form:service_conf')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="odds">
					<th rowspan="2"><?=lang('form:recaptcha')?></th>
					<th class="subhead"><?=lang('form:recaptcha:public')?></th>
					<td><?=form_input('settings[recaptcha][public]', $config['recaptcha']['public'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead"><?=lang('form:recaptcha:private')?></th>
					<td><?=form_input('settings[recaptcha][private]', $config['recaptcha']['private'])?></td>
				</tr>

				<tr class="evens">
					<th><?=lang('form:mailchimp_settings')?></th>
					<th class="subhead"><?=lang('form:api_key')?></th>
					<td><?=form_input('settings[mailchimp][api_key]', $config['mailchimp']['api_key'])?></td>
				</tr>

				<tr class="odds">
					<th rowspan="2"><?=lang('form:createsend_settings')?></th>
					<th class="subhead"><?=lang('form:api_key')?></th>
					<td><?=form_input('settings[createsend][api_key]', $config['createsend']['api_key'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead"><?=lang('form:client_api_key')?></th>
					<td><?=form_input('settings[createsend][client_api_key]', $config['createsend']['client_api_key'])?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="FormTable">
		<h2><?=lang('f:payment_prov')?></h2>
		<table class="hasrowspan">
			<thead>
				<tr>
					<th><?=lang('form:service')?></th>
					<th colspan="2"><?=lang('form:service_conf')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="odds">
					<th rowspan="4"><?=lang('f:stripe')?></th>
					<th class="subhead"><?=lang('f:test')?>: <?=lang('f:secret_key')?></th>
					<td><?=form_input('settings[stripe][test][secret]', $config['stripe']['test']['secret'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead"><?=lang('f:test')?>: <?=lang('f:public_key')?></th>
					<td><?=form_input('settings[stripe][test][public]', $config['stripe']['test']['public'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead"><?=lang('f:live')?>: <?=lang('f:secret_key')?></th>
					<td><?=form_input('settings[stripe][live][secret]', $config['stripe']['live']['secret'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead"><?=lang('f:live')?>: <?=lang('f:public_key')?></th>
					<td><?=form_input('settings[stripe][live][public]', $config['stripe']['live']['public'])?></td>
				</tr>

				<tr class="odds">
					<th rowspan="2"><?=lang('f:aunet')?></th>
					<th class="subhead">API Login ID</th>
					<td><?=form_input('settings[aunet][api_login_id]', $config['aunet']['api_login_id'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead">Transaction Key</th>
					<td><?=form_input('settings[aunet][transaction_key]', $config['aunet']['transaction_key'])?></td>
				</tr>

				<tr class="odds">
					<th rowspan="4"><?=lang('f:payflow')?></th>
					<th class="subhead">Vendor</th>
					<td><?=form_input('settings[payflow_pro][vendor]', $config['payflow_pro']['vendor'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead">Merchant Partner</th>
					<td><?=form_input('settings[payflow_pro][partner]', $config['payflow_pro']['partner'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead">Username</th>
					<td><?=form_input('settings[payflow_pro][username]', $config['payflow_pro']['username'])?></td>
				</tr>
				<tr class="odds">
					<th class="subhead">Password</th>
					<td><?=form_input('settings[payflow_pro][password]', $config['payflow_pro']['password'])?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="FormTable">
		<h2><?=lang('form:print_pdf')?></h2>
		<table class="hasrowspan">
			<thead>
				<tr>
					<th><?=lang('form:option')?></th>
					<th><?=lang('form:value')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="odds">
					<td><?=lang('form:paper_size')?></td>
					<td><?=form_dropdown('settings[print_pdf][paper_size]', $pdf_paper_sizes, $config['print_pdf']['paper_size'])?></td>
				</tr>
				<tr class="evens">
					<td><?=lang('form:orientation')?></td>
					<td>
						<?=form_radio('settings[print_pdf][paper_orientation]', 'portrait',  (($config['print_pdf']['paper_orientation'] == 'portrait') ? TRUE : FALSE) )?>&nbsp;<?=lang('form:portrait')?>&nbsp;&nbsp;&nbsp;
						<?=form_radio('settings[print_pdf][paper_orientation]', 'landscape',  (($config['print_pdf']['paper_orientation'] == 'landscape') ? TRUE : FALSE) )?>&nbsp;<?=lang('form:landscape')?>
					</td>
				</tr>
				<tr class="odds">
					<td><?=lang('form:pdf_template_top')?></td>
					<td><?=form_textarea('settings[print_pdf][template_top]', $config['print_pdf']['template_top'])?></td>
				</tr>
				<tr class="odds">
					<td><?=lang('form:pdf_template_loop')?></td>
					<td><?=form_textarea('settings[print_pdf][template_loop]', $config['print_pdf']['template_loop'])?></td>
				</tr>
			</tbody>
		</table>
	</div>

<input class="submit" type="submit" value="<?=lang('form:save')?>">

<?=form_close()?>
</div>

</div><!--fbody-->

<?php echo $this->view('mcp/_footer'); ?>
