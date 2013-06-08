<tr>
	<td><?=lang('form:choices')?> <span class="pophelp" data-key="choices" title="<?=lang('form:choices')?>"></span></td>
	<td>
		<table cellspacing="0" cellpadding="0" border="0" style="width:100%" class="FormsChoicesTable">
			<thead>
				<th style="width:15px;"></th>
				<th><?=lang('form:label')?></th>
				<th class="choices_values"><?=lang('form:value')?></th>
				<th style="width:40px;"></th>
			</thead>
			<tbody>
				<?php foreach($choices as $number => $choice): ?>
				<tr>
					<td><?=form_radio($form_name_settings.'[default_choice]', $number, ((isset($default_choice) != FALSE && $default_choice === $number) ? TRUE : FALSE), ' class="ChoicesDefault" ')?></td>
					<td><?=form_input($form_name_settings.'[choices]['.$number.'][label]', $choice['label']);?></td>
					<td class="choices_values"><?=form_input($form_name_settings.'[choices]['.$number.'][value]', $choice['value']);?></td>
					<td><a href="#" class="AddChoice"></a> <a href="#" class="RemoveChoice"></a></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="4">
						<a href="#" class="BulkAddChoices"><?=lang('form:bulkadd');?></a>
						<a href="#" class="BulkRemoveAll" style="float:right"><?=lang('form:remove_all');?></a>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
</tr>

<tr>
	<td><?=lang('form:enable_values')?> <span class="pophelp" data-key="enable_values" title="<?=lang('form:enable_values')?>"></span></td>
	<td>
		<?=form_radio($form_name_settings.'[values_enabled]', 'no', ((isset($values_enabled) == FALSE OR $values_enabled== 'no') ? TRUE : FALSE), ' class="ChoicesEnableVal" ')?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[values_enabled]', 'yes', ((isset($values_enabled) == TRUE && $values_enabled== 'yes') ? TRUE : FALSE), ' class="ChoicesEnableVal" ')?> <?=lang('form:yes')?>
	</td>
</tr>

<!--
<tr>
	<td><?=lang('form:enable_other')?> <span class="pophelp" data-key="enable_other_choice"></span></td>
	<td>
		<?=form_radio($form_name_settings.'[enable_other]', 'no', ((isset($enable_other) == FALSE OR $enable_other == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[enable_other]', 'yes', ((isset($enable_other) == TRUE && $enable_other == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>
-->