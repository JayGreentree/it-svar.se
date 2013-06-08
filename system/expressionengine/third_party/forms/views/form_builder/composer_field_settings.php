<div class="field_settings">
<div class="FormTable">
<h2>
	<?=lang('form:field_settings')?>
	<input type="submit" class="submit save_field" data-action="save" value="<?=lang('form:save_settings')?>">
	<a href="#" class="save_field" data-action="cancel"><?=lang('form:cancel')?></a>
	<span class="saving_settings"><?=lang('form:saving_settings')?></span>
</h2>
<table>

	<tbody>
		<?php if ($disable_title == FALSE):?>
		<tr>
			<td><?=lang('form:field_label')?> <span class="pophelp" data-key="field_label" title="<?=lang('form:field_label')?>"></span></td>
			<td><input name="settings[title]" type="text" value="<?=$title?>" id="fieldsettings_field_title"/></td>
		</tr>
		<tr>
			<td><?=lang('form:field_short_name')?> <span class="pophelp" data-key="field_short_name" title="<?=lang('form:field_short_name')?>"></span></td>
			<td><input name="settings[url_title]" type="text" value="<?=$url_title?>" id="fieldsettings_field_url_title"/></td>
		</tr>
		<tr>
			<td><?=lang('form:field_desc')?> <span class="pophelp" data-key="field_desc" title="<?=lang('form:field_desc')?>"></span></td>
			<td><textarea name="settings[description]"><?=$description?></textarea></td>
		</tr>
		<tr>
			<td><?=lang('form:required')?> <span class="pophelp" data-key="required_field" title="<?=lang('form:required')?>"></span></td>
			<td>
				<?=form_radio('settings[required]', 'yes', (($required == 1) ? TRUE : FALSE) )?> <?=lang('form:yes')?>&nbsp;&nbsp;
				<?=form_radio('settings[required]', 'no', (($required == 0) ? TRUE : FALSE) )?> <?=lang('form:no')?>
			</td>
		</tr>
		<tr>
			<td><?=lang('form:show_label')?> <span class="pophelp" data-key="show_field_label" title="<?=lang('form:show_label')?>"></span></td>
			<td>
				<?=form_radio('settings[show_label]', '1', (($show_label == 1) ? TRUE : FALSE) )?> <?=lang('form:label:auto')?>&nbsp;&nbsp;
				<?=form_radio('settings[show_label]', '2', (($show_label == 2) ? TRUE : FALSE) )?> <?=lang('form:label:show')?>&nbsp;&nbsp;
				<?=form_radio('settings[show_label]', '0', (($show_label == 0) ? TRUE : FALSE) )?> <?=lang('form:label:hide')?>
			</td>
		</tr>
		<tr>
			<td><?=lang('form:label_position')?></td>
			<td>
				<?=form_radio('settings[label_position]', 'auto', (($label_position == 'auto') ? TRUE : FALSE))?> <?=lang('form:label:auto')?> &nbsp;&nbsp;
				<?=form_radio('settings[label_position]', 'top', (($label_position == 'top') ? TRUE : FALSE))?> <?=lang('form:place:top')?> &nbsp;&nbsp;
				<?=form_radio('settings[label_position]', 'left_align', (($label_position == 'left_align') ? TRUE : FALSE))?> <?=lang('form:place:left_align')?> &nbsp;&nbsp;
				<?=form_radio('settings[label_position]', 'right_align', (($label_position == 'right_align') ? TRUE : FALSE))?> <?=lang('form:place:right_align')?> &nbsp;&nbsp;
				<?=form_radio('settings[label_position]', 'bottom', (($label_position == 'bottom') ? TRUE : FALSE))?> <?=lang('form:place:bottom')?>
			</td>
		</tr>
		<?php else:?>
		<input name="settings[label_position]" type="hidden" value="auto"/>
		<input name="settings[show_label]" type="hidden" value="auto"/>
		<input name="settings[required]" type="hidden" value="0"/>
		<?php endif;?>
		<input name="settings[field_type]" type="hidden" class="hType" value="<?=$field_type?>"/>
		<input name="settings[field_id]" type="hidden" value="<?=$field_id?>"/>
		<input name="settings[field_hash]" type="hidden" value="<?=$field_hash?>" class="field_hash"/>
	</tbody>
</table>
<table>
	<h2><?=lang('form:field_spec_settings')?></h2>
	<tbody>
		<?=$field_body_settings?>
	</tbody>
	<tbody>
		<tr>
			<td><?=lang('form:enable_cond')?> <span class="pophelp" data-key="enable_cond" title="<?=lang('form:enable_cond')?>"></span></td>
			<td class="ShowHideSubmitBtn">
				<?=form_radio('settings[conditionals_options][enable]', 'no', ((isset($conditionals['options']['enable']) == FALSE OR $conditionals['options']['enable'] == 'no') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel=foo" ' )?> <?=lang('form:no')?>&nbsp;&nbsp;
				<?=form_radio('settings[conditionals_options][enable]', 'yes', ((isset($conditionals['options']['enable']) == TRUE && $conditionals['options']['enable'] == 'yes') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="conditionals" ' )?> <?=lang('form:yes')?>

				<div class="btn_conditionals showhide" style="display:none"><br>
					<?=form_dropdown('settings[conditionals_options][display]', array('show'=>'Show', 'hide'=>'Hide'), ((isset($conditionals['options']['display']) == TRUE) ? $conditionals['options']['display'] : ''))?>&nbsp;
					<?=lang('form:cond:this_field_id')?>&nbsp;
					<?=form_dropdown('settings[conditionals_options][match]', array('all'=>'All', 'any'=>'Any'), ((isset($conditionals['options']['match']) == TRUE) ? $conditionals['options']['match'] : '') )?>&nbsp;
					<?=lang('form:conf:following_match');?>
					<br><br>
					<table cellspacing="0" cellpadding="0" border="0" style="width:100%" class="FormsChoicesTable ConditionalTable">
					<thead>
						<th><?=lang('form:field')?></th>
						<th><?=lang('form:condition')?></th>
						<th><?=lang('form:value')?></th>
						<th style="width:40px;"></th>
					</thead>
					<tbody>
						<?php
						$options = array();
						$options['is'] = 'is';
						$options['isnot'] = 'is not';
						$options['greater_then'] = 'greater then';
						$options['less_then'] = 'less then';
						$options['contains'] = 'contains';
						$options['starts_with'] = 'starts with';
						$options['ends_with'] = 'ends with';
						?>


						<?php if (empty($conditionals['conditionals']) === TRUE):?>
						<tr>
							<td>
								<select name="settings[conditionals][0][field]" data-selected=""></select>
							</td>
							<td>
								<?=form_dropdown('settings[conditionals][0][operator]', $options)?>
							</td>
							<td><?=form_input('settings[conditionals][0][value]', '')?></td>
							<td><a href="#" class="AddChoice"></a> <a href="#" class="RemoveChoice"></a></td>
						</tr>
						<?php else:?>


						<?php foreach ($conditionals['conditionals'] as $key => $cond):?>
						<tr>
							<td> <?php /* Refreshing field should submit this select! */ ?>
								<select name="settings[conditionals][<?=$key?>][field]" data-selected="<?=$cond['field']?>">
									<option value="<?=$cond['field']?>"><?=$cond['field']?></option>
								</select>
							</td>
							<td>
								<?=form_dropdown('settings[conditionals]['.$key.'][operator]', $options, $cond['operator'])?>
							</td>
							<td><?=form_input('settings[conditionals]['.$key.'][value]', $cond['value'])?></td>
							<td><a href="#" class="AddChoice"></a> <a href="#" class="RemoveChoice"></a></td>
						</tr>
						<?php endforeach;?>


						<?php endif;?>
					</tbody>
					</table>

				</div>
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>
