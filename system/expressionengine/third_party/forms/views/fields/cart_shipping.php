<tr>
	<td><?=lang('f:fieldtype')?></td>
	<td class="shide_triggers">
		<?=form_radio($form_name_settings.'[field_type]', 'single', ((isset($field_type) == FALSE OR $field_type== 'single') ? TRUE : FALSE), '  ')?> <?=lang('f:single_method')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'select', ((isset($field_type) == TRUE && $field_type== 'select') ? TRUE : FALSE), '  ')?> <?=lang('f:dropdown')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'radio', ((isset($field_type) == TRUE && $field_type== 'radio') ? TRUE : FALSE), '  ')?> <?=lang('f:radio_button')?>&nbsp;&nbsp;
	</td>
</tr>

<tr class="shide s-single s-select s-radio">
	<td><?=lang('f:display_tmpl')?></td>
	<td><?=form_input($form_name_settings.'[display_tmpl]', $display_tmpl)?></td>
</tr>

<tr class="shide s-single s-user_price s-hidden">
	<td><?=lang('f:shipping_label')?></td>
	<td><?=form_input($form_name_settings.'[shipping_label]', $shipping_label)?></td>
</tr>

<tr class="shide s-single s-hidden">
	<td><?=lang('f:shipping_price')?></td>
	<td><?=form_input($form_name_settings.'[shipping_price]', $shipping_price)?></td>
</tr>

<tr class="shide s-select s-radio">
	<td><?=lang('f:shipping_options')?></td>
	<td>
		<table cellspacing="0" cellpadding="0" border="0" style="width:100%" class="FormsChoicesTable">
			<thead>
				<th style="width:15px;"></th>
				<th><?=lang('form:label')?></th>
				<th class="choices_values"><?=lang('form:value')?></th>
				<th><?=lang('f:price')?></th>
				<th style="width:40px;"></th>
			</thead>
			<tbody>
				<?php foreach($choices as $number => $choice): ?>
				<tr>
					<td><?=form_radio($form_name_settings.'[default_choice]', $number, ((isset($default_choice) != FALSE && $default_choice === $number) ? TRUE : FALSE), ' class="ChoicesDefault" ')?></td>
					<td><?=form_input($form_name_settings.'[choices]['.$number.'][label]', $choice['label']);?></td>
					<td class="choices_values"><?=form_input($form_name_settings.'[choices]['.$number.'][value]', $choice['value']);?></td>
					<td><?=form_input($form_name_settings.'[choices]['.$number.'][price]', $choice['price']);?></td>
					<td><a href="#" class="AddChoice"></a> <a href="#" class="RemoveChoice"></a></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">
						<a href="#" class="BulkRemoveAll" style="float:right"><?=lang('form:remove_all');?></a>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
</tr>

<tr class="shide s-select s-radio">
	<td><?=lang('form:enable_values')?> <span class="pophelp" data-key="enable_values" title="<?=lang('form:enable_values')?>"></span></td>
	<td>
		<?=form_radio($form_name_settings.'[values_enabled]', 'no', ((isset($values_enabled) == FALSE OR $values_enabled== 'no') ? TRUE : FALSE), ' class="ChoicesEnableVal" ')?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[values_enabled]', 'yes', ((isset($values_enabled) == TRUE && $values_enabled== 'yes') ? TRUE : FALSE), ' class="ChoicesEnableVal" ')?> <?=lang('form:yes')?>
	</td>
</tr>
