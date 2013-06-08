<tr>
	<td><?=lang('form:allowed_ext')?> <span class="pophelp" data-key="allowed_ext" title="<?=lang('form:allowed_ext')?>"></span></td>
	<td>
		<?=form_input($form_name_settings.'[extensions]', ((isset($extensions) == TRUE) ? $extensions : ''))?>
		<small><?=lang('form:help:allowed_ext')?></small>
	</td>
</tr>

<tr>
	<td><?=lang('form:max_filesize')?></td>
	<td>
		<?=form_input($form_name_settings.'[filesize]', ((isset($filesize) == TRUE) ? $filesize : ''))?>
	</td>
</tr>

<tr>
	<td><?=lang('form:upload_destination')?></td>
	<td>
		<?=form_dropdown($form_name_settings.'[upload_destination]', $prefs, ((isset($upload_destination) == TRUE) ? $upload_destination : ''))?>
	</td>
</tr>
