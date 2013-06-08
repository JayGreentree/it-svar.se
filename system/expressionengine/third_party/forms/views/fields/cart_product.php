<tr>
	<td><?=lang('f:fieldtype')?></td>
	<td class="shide_triggers">
		<?=form_radio($form_name_settings.'[field_type]', 'single', ((isset($field_type) == FALSE OR $field_type == 'single') ? TRUE : FALSE), '  ')?> <?=lang('f:single_product')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'select', ((isset($field_type) == TRUE && $field_type == 'select') ? TRUE : FALSE), '  ')?> <?=lang('f:dropdown')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'radio', ((isset($field_type) == TRUE && $field_type == 'radio') ? TRUE : FALSE), '  ')?> <?=lang('f:radio_button')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'checkbox', ((isset($field_type) == TRUE && $field_type == 'checkbox') ? TRUE : FALSE), '  ')?> <?=lang('f:checkbox')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'user_price', ((isset($field_type) == TRUE && $field_type == 'user_price') ? TRUE : FALSE), '  ')?> <?=lang('f:user_price')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'entry_product', ((isset($field_type) == TRUE && $field_type == 'entry_product') ? TRUE : FALSE), '  ')?> <?=lang('f:entry_product')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[field_type]', 'hidden', ((isset($field_type) == TRUE && $field_type == 'hidden') ? TRUE : FALSE), '  ')?> <?=lang('f:hidden')?>
	</td>
</tr>

<tr class="shide s-single s-select s-radio s-checkbox s-entry_product">
	<td><?=lang('f:display_tmpl')?></td>
	<td><?=form_input($form_name_settings.'[display_tmpl]', $display_tmpl)?></td>
</tr>

<tr class="shide s-single s-user_price s-hidden">
	<td><?=lang('f:product_label')?></td>
	<td><?=form_input($form_name_settings.'[product_label]', $product_label)?></td>
</tr>

<tr class="shide s-single s-user_price s-hidden">
	<td><?=lang('f:product_price')?></td>
	<td><?=form_input($form_name_settings.'[product_price]', $product_price)?></td>
</tr>

<tr class="shide s-single s-radio s-checkbox s-entry_product">
	<td><?=lang('f:show_qty')?></td>
	<td>
		<?=form_radio($form_name_settings.'[show_qty]', 'yes', ((isset($show_qty) == FALSE OR $show_qty == 'yes') ? TRUE : FALSE), '  ')?> <?=lang('yes')?>&nbsp;&nbsp;
		<?=form_radio($form_name_settings.'[show_qty]', 'no', ((isset($show_qty) == TRUE && $show_qty == 'no') ? TRUE : FALSE), '  ')?> <?=lang('no')?>&nbsp;&nbsp;
	</td>
</tr>

<tr class="shide s-single s-radio s-checkbox">
	<td><?=lang('f:default_qty')?></td>
	<td><?=form_input($form_name_settings.'[default_qty]', $default_qty)?></td>
</tr>

<tr class="shide s-select s-radio s-checkbox">
	<td><?=lang('f:products')?></td>
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

<tr class="shide s-select s-radio s-checkbox">
	<td><?=lang('form:enable_values')?> <span class="pophelp" data-key="enable_values" title="<?=lang('form:enable_values')?>"></span></td>
	<td>
		<?=form_radio($form_name_settings.'[values_enabled]', 'no', ((isset($values_enabled) == FALSE OR $values_enabled== 'no') ? TRUE : FALSE), ' class="ChoicesEnableVal" ')?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[values_enabled]', 'yes', ((isset($values_enabled) == TRUE && $values_enabled== 'yes') ? TRUE : FALSE), ' class="ChoicesEnableVal" ')?> <?=lang('form:yes')?>
	</td>
</tr>

<tr class="shide s-entry_product">
	<td><?=lang('f:product_price_field')?></td>
	<td><?=form_dropdown($form_name_settings.'[product_price_field]', $dbfields, $product_price_field);?></td>
</tr>

<tr class="shide s-entry_product">
	<td><?=lang('f:product_label_field')?></td>
	<td>
		<?php $dbfields = array('entry_title' => 'Entry Title') + $dbfields; ?>
		<?=form_dropdown($form_name_settings.'[product_label_field]', $dbfields, $product_label_field);?>
	</td>
</tr>
