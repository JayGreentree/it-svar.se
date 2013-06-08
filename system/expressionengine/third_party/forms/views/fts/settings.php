<div class="Forms">
<ul class="Tabs">
	<li><a href="#FormBuilder"><?=lang('form:builder')?></a></li>
	<!--<li><a href="#FormAlerts"><?=lang('form:alerts')?></a></li>
	<li><a href="#FormSettings"><?=lang('form:adv_settings')?></a></li>-->
</ul>

<div class="TabsHolder">
<div class="FormBuilder cfix" id="FormBuilder">

<?php foreach($fields as $category => $list):?>
	<div class="block">
		<h6><?=lang("form:{$category}")?></h6>
		<table cellspacing="0" cellpadding="0" border="0" class="fieldlist">
		<thead>
			<tr>
				<th style="width:50px"><?=lang('form:enabled')?></th>
				<th style="width:200px"><?=lang('form:field_name')?></th>
				<th><?=lang('form:settings')?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($list as $field):?>
			<?php $checked = ($field['checked'] == TRUE) ? 'checked' : '';?>

			<tr class="<?=$checked?>">
				<td><input type="checkbox" value="<?=$field['name']?>" name="forms[fields][<?=$category?>][]" <?=$checked?> class="toggler"></td>
				<td>
					<img src="<?=FORMS_THEME_URL?>fields/<?=$field['name']?>.png" />
					<strong><?=$field['label']?></strong>
				</td>
				<td>
					<?php if ($field['settings'] != FALSE):?>
					<div class="CFsettings">
						<a href="#" class="SettingsToggler sHidden" rel="<?=lang('form:hide_settings')?>"><?=lang('form:show_settings')?></a>
						<div class="fsettings">

							<table cellspacing="0" cellpadding="0" border="0" class="stable">
							<thead>
								<tr>
									<th colspan="10"><?=lang('form:settings')?></th>
								</tr>
							</thead>
							<tbody>
								<?=$field['settings']?>
							</tbody>
							</table>
						</div>
					</div>
					<?php endif;?>
				</td>
			</tr>

		<?php endforeach;?>
		</tbody>
		</table>
	</div>
<?php endforeach;?>

</div>


</div>
</div>

<script type="text/javascript">
var Forms = Forms ? Forms : new Object();
Forms.JSON = Forms.JSON ? Forms.JSON : new Object();
<?php if (isset($helpjson) == TRUE):?>Forms.JSON.Help = <?=$helpjson?>;<?php endif;?>
</script>
