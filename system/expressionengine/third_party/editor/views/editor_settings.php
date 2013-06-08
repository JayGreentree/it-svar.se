<div class="EditorField dtable">

	<h2>
		<?=lang('ed:editor_settings')?>
		&nbsp;&nbsp;

		<?php if (isset($config_id) === FALSE):?>
		<span class="editor_settings_toggler" style="font-size:12px">
			<input name="<?=$field_name?>[editor_settings]" type="radio" value="predefined" <?php if ($editor_settings == 'predefined') echo 'checked'?>> <?=lang('ed:predefined')?>&nbsp;&nbsp;
			<input name="<?=$field_name?>[editor_settings]" type="radio" value="custom" <?php if ($editor_settings == 'custom') echo 'checked'?>> <?=lang('ed:custom')?>&nbsp;&nbsp;
		</span>

		<span class="editor_fieldtype_toggler">
			<strong><?=lang('ed:convert_entries')?></strong>
			<input name="<?=$field_name?>[convert_field]" type="radio" value="none" checked="checked"> <?=lang('ed:no')?>&nbsp;&nbsp;
			<input name="<?=$field_name?>[convert_field]" type="radio" value="auto"> Auto &lt;br /&gt; or XHTML&nbsp;&nbsp;
			<br><small><?=lang('ed:convert_entries_exp')?></small>
		</span>
		<?php endif;?>
	</h2>

<div class="editor_settings_wrapper editor_settings_custom" <?php if (isset($config_id) === FALSE):?>style="display:none"<?php endif;?> >

	<div class="dmenu">
	<ul>
		<li class="current"><a href="#" data-section="tbuttons"><?=lang('ed:toolbar_buttons')?></a></li>
		<li><a href="#" data-section="upload_settings"><?=lang('ed:upload_settings')?></a></li>
		<li><a href="#" data-section="adv_settings"><?=lang('ed:adv_settings')?></a></li>
	</ul>
	</div>

	<table class="tabholder tbuttons">
		<tbody>
			<tr>
				<td><label><?=lang('ed:buttons')?></label></td>
				<td>
					<ul class="redactor_toolbar buttons_current" style="border:1px solid #BBB;">
						<?php foreach($buttons as $btn):?>

						<?php if ($btn == '|'):?>
						<li class="redactor_separator" title="<?=lang('ed:btn:sep')?>">
							<input name="<?=$field_name?>[buttons][]" value="|" type="hidden">
						</li>
						<?php continue;?>
						<?php endif;?>

						<li>
							<a class="redactor_btn_<?=$btn?>" title="<?=lang('ed:btn:'.$btn)?>" href="javascript:void(null);"></a>
							<input name="<?=$field_name?>[buttons][]" value="<?=$btn?>" type="hidden">
						</li>
						<?php endforeach;?>
					</ul>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:buttons_avail')?></label></td>
				<td>
					<ul class="redactor_toolbar buttons_avail" style="border:1px solid #BBB;">
						<?php foreach($all_buttons as $btn):?>
						<?php if (in_array($btn, $buttons) === TRUE) continue;?>
						<li>
							<a class="redactor_btn_<?=$btn?>" title="<?php if (isset($this->editor->buttons[$btn]) === TRUE) echo $this->editor->buttons[$btn]->info['name']; else echo lang('ed:btn:'.$btn);?>" href="javascript:void(null);"></a>
							<input name="<?=$field_name?>[buttons][]" value="<?=$btn?>" type="hidden" disabled>
						</li>
						<?php endforeach;?>

						<li class="redactor_separator" title="<?=lang('ed:btn:sep')?>">
							<input name="<?=$field_name?>[buttons][]" value="|" type="hidden" disabled>
						</li>
					</ul>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="tabholder upload_settings hidden">
		<tbody>
			<tr>
				<td style="width:300px"><label><?=lang('ed:act_url')?></label></td>
				<td>
					<a href="<?=$act_url?>" target="_blank"><?=$act_url?></a>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:upload_service')?></label></td>
				<td class="upload_service">
					<input name="<?=$field_name?>[upload_service]" type="radio" value="local" <?php if ($upload_service == 'local') echo 'checked'?>> <?=lang('ed:local')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[upload_service]" type="radio" value="s3" <?php if ($upload_service == 's3') echo 'checked'?>> <?=lang('ed:s3')?>&nbsp;&nbsp;
				</td>
			</tr>
		</tbody>
		<tbody class="upload_wrapper upload_local">
			<tr>
				<td><label><?=lang('ed:file_upload_loc')?></label></td>
				<td>
					<?=form_dropdown($field_name.'[file_upload_location]', $locations, $file_upload_location)?>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:image_upload_loc')?></label></td>
				<td>
					<?=form_dropdown($field_name.'[image_upload_location]', $locations, $image_upload_location)?>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:allow_img_browsing')?></label></td>
				<td>
					<input name="<?=$field_name?>[image_browsing]" type="radio" value="yes" <?php if ($image_browsing == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[image_browsing]" type="radio" value="no" <?php if ($image_browsing == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:allow_img_subdir')?></label></td>
				<td>
					<input name="<?=$field_name?>[image_subdir]" type="radio" value="yes" <?php if ($image_subdir == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[image_subdir]" type="radio" value="no" <?php if ($image_subdir == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
		</tbody>
		<tbody class="upload_wrapper upload_s3">
			<tr>
				<td><label><?=lang('ed:s3:bucket_file')?></label></td>
				<td>
					<?=form_input($field_name.'[s3][file][bucket]', $s3['file']['bucket'])?>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:s3:bucket_image')?></label></td>
				<td>
					<?=form_input($field_name.'[s3][image][bucket]', $s3['image']['bucket'])?>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:s3:aws_key')?></label></td>
				<td>
					<?php if (isset($config_override['s3']['aws_access_key']) === TRUE && $config_override['s3']['aws_access_key'] != FALSE):?>
					<?=form_input('', $config_override['s3']['aws_access_key'], ' disabled=disabled ')?>
					<?=form_hidden($field_name.'[s3][aws_access_key]', $s3['aws_access_key'])?>
					<?php else:?>
					<?=form_input($field_name.'[s3][aws_access_key]', $s3['aws_access_key'])?>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:s3:aws_secret_key')?></label></td>
				<td>
					<?php if (isset($config_override['s3']['aws_secret_key']) === TRUE && $config_override['s3']['aws_secret_key'] != FALSE):?>
					<?=form_password('', $config_override['s3']['aws_secret_key'], ' disabled=disabled ')?>
					<?=form_hidden($field_name.'[s3][aws_secret_key]', $s3['aws_secret_key'])?>
					<?php else:?>
					<?=form_password($field_name.'[s3][aws_secret_key]', $s3['aws_secret_key'])?>
					<?php endif;?>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="tabholder adv_settings hidden">
		<tbody>
			<tr>
				<td style="width:300px"><label><?=lang('ed:height')?></label></td>
				<td>
					<input name="<?=$field_name?>[height]" type="text" value="<?=$height?>">
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:direction')?></label></td>
				<td>
					<input name="<?=$field_name?>[direction]" type="radio" value="ltr" <?php if ($direction == 'ltr') echo 'checked'?>> <?=lang('ed:ltr')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[direction]" type="radio" value="rtl" <?php if ($direction == 'rtl') echo 'checked'?>> <?=lang('ed:rtl')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:enable_toolbar')?></label></td>
				<td>
					<input name="<?=$field_name?>[toolbar]" type="radio" value="yes" <?php if ($toolbar == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[toolbar]" type="radio" value="no" <?php if ($toolbar == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:enable_source')?></label></td>
				<td>
					<input name="<?=$field_name?>[source]" type="radio" value="yes" <?php if ($source == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[source]" type="radio" value="no" <?php if ($source == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:focus')?></label></td>
				<td>
					<input name="<?=$field_name?>[focus]" type="radio" value="yes" <?php if ($focus == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[focus]" type="radio" value="no" <?php if ($focus == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:autoresize')?></label></td>
				<td>
					<input name="<?=$field_name?>[autoresize]" type="radio" value="yes" <?php if ($autoresize == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[autoresize]" type="radio" value="no" <?php if ($autoresize == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:fixed')?></label></td>
				<td>
					<input name="<?=$field_name?>[fixed]" type="radio" value="yes" <?php if ($fixed == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[fixed]" type="radio" value="no" <?php if ($fixed == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:convertlinks')?></label></td>
				<td>
					<input name="<?=$field_name?>[convertlinks]" type="radio" value="yes" <?php if ($convertlinks == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[convertlinks]" type="radio" value="no" <?php if ($convertlinks == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:convertdivs')?></label></td>
				<td>
					<input name="<?=$field_name?>[convertdivs]" type="radio" value="yes" <?php if ($convertdivs == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[convertdivs]" type="radio" value="no" <?php if ($convertdivs == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:overlay')?></label></td>
				<td>
					<input name="<?=$field_name?>[overlay]" type="radio" value="yes" <?php if ($overlay == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[overlay]" type="radio" value="no" <?php if ($overlay == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:observeimages')?></label></td>
				<td>
					<input name="<?=$field_name?>[observeimages]" type="radio" value="yes" <?php if ($observeimages == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[observeimages]" type="radio" value="no" <?php if ($observeimages == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:key_shortcuts')?></label></td>
				<td>
					<input name="<?=$field_name?>[shortcuts]" type="radio" value="yes" <?php if ($shortcuts == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[shortcuts]" type="radio" value="no" <?php if ($shortcuts == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:air')?></label></td>
				<td>
					<input name="<?=$field_name?>[air]" type="radio" value="yes" <?php if ($air == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[air]" type="radio" value="no" <?php if ($air == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:wym')?></label></td>
				<td>
					<input name="<?=$field_name?>[wym]" type="radio" value="yes" <?php if ($wym == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[wym]" type="radio" value="no" <?php if ($wym == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:protocol_links')?></label></td>
				<td>
					<input name="<?=$field_name?>[protocol]" type="radio" value="yes" <?php if ($protocol == 'yes') echo 'checked'?>> <?=lang('ed:yes')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[protocol]" type="radio" value="no" <?php if ($protocol == 'no') echo 'checked'?>> <?=lang('ed:no')?>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:allowedtags')?></label></td>
				<td>
					<input name="<?=$field_name?>[allowedtags_option]" type="radio" value="default" <?php if ($allowedtags_option == 'default') echo 'checked'?>> <?=lang('ed:default')?>&nbsp;&nbsp;
					<input name="<?=$field_name?>[allowedtags_option]" type="radio" value="custom" <?php if ($allowedtags_option == 'custom') echo 'checked'?>> <?=lang('ed:custom')?>&nbsp;&nbsp;<br>
					<input name="<?=$field_name?>[allowedtags]" type="text" value="<?=implode(',', $allowedtags)?>" placeholder="<?=lang('ed:allowedtags_custom')?>" style="margin-top:8px;">
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:formattingtags')?></label></td>
				<td>
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="p" <?php if (in_array('p', $formattingtags)) echo 'checked'?>> p &nbsp;&nbsp;
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="blockquote" <?php if (in_array('blockquote', $formattingtags)) echo 'checked'?>> blockquote &nbsp;&nbsp;
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="pre" <?php if (in_array('pre', $formattingtags)) echo 'checked'?>> pre &nbsp;&nbsp;
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="h1" <?php if (in_array('h1', $formattingtags)) echo 'checked'?>> h1 &nbsp;&nbsp;
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="h2" <?php if (in_array('h2', $formattingtags)) echo 'checked'?>> h2 &nbsp;&nbsp;
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="h3" <?php if (in_array('h3', $formattingtags)) echo 'checked'?>> h3 &nbsp;&nbsp;
					<input name="<?=$field_name?>[formattingtags][]" type="checkbox" value="h4" <?php if (in_array('h4', $formattingtags)) echo 'checked'?>> h4 &nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:language')?></label></td>
				<td>
					<?php $langs = array('en' => 'English', 'sq' => 'Albanian', 'ar' => 'Arabic', 'es_ar ' => 'Argentinian Spanish', 'by' => 'Belorussian', 'ba' => 'Bosnian', 'pt_br ' => 'Brazilian Portuguese', 'bg' => 'Bulgarian', 'ca' => 'Catalan', 'zh_cn ' => 'Chinese Simplified', 'zh_tw ' => 'Chinese Traditional', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'eo' => 'Esperanto', 'fi' => 'Finnish', 'fr' => 'French', 'de' => 'German', 'el' => 'Greek', 'hu' => 'Hungarian', 'id' => 'Indonesian', 'it' => 'Italian', 'ja' => 'Japanese', 'ko' => 'Korean', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'no_NB ' => 'Norwegian (Bokmål)', 'fa' => 'Persian', 'pl' => 'Polish', 'ro' => 'Romanian', 'ru' => 'Russian', 'cir' => 'Serbian (Cyrillic)  sr-', 'lat' => 'Serbian (Latin) sr-', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'es' => 'Spanish', 'sv' => 'Swedish', 'tr' => 'Turkish', 'ua' => 'Ukrainian', 'vi' => 'Vietnamese');?>
					<?=form_dropdown($field_name.'[language]', $langs, $language);?>
				</td>
			</tr>
			<tr>
				<td><label><?=lang('ed:css_file')?></label></td>
				<td>
					<input name="<?=$field_name?>[css_file]" type="text" value="<?=$css_file?>">
					<small><?=lang('ed:css_file:help')?></small>
				</td>
			</tr>
		</tbody>
	</table>
</div>



<?php if (isset($config_id) === FALSE):?>
<div class="editor_settings_wrapper editor_settings_predefined">
	<table>
		<tbody>
			<tr>
				<td style="width:200px"><label><?=lang('ed:editor_conf')?></label></td>
				<td>
					<?=form_dropdown($field_name.'[editor_conf]', $editors_confs, $editor_conf)?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php endif;?>


</div>
