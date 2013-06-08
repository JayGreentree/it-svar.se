<tr>
	<td><?=lang('form:captcha_loggedin')?></td>
	<td>
		<?=form_radio($form_name_settings.'[captcha_for_members]', 'no', ((isset($captcha_for_members) == FALSE OR $captcha_for_members == 'no') ? TRUE : FALSE))?> <?=lang('form:no')?>
		<?=form_radio($form_name_settings.'[captcha_for_members]', 'yes', ((isset($captcha_for_members) == TRUE && $captcha_for_members == 'yes') ? TRUE : FALSE))?> <?=lang('form:yes')?>
	</td>
</tr>
<tr>
	<td><?=lang('form:captcha_type')?></td>
	<td class="ShowHideSubmitBtn">
		<?=form_radio($form_name_settings.'[type]', 'simple', ((isset($type) == FALSE OR $type == 'simple') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="simple" ')?> <?=lang('form:standard_capt')?>
		<?=form_radio($form_name_settings.'[type]', 'recaptcha', ((isset($type) == TRUE && $type == 'recaptcha') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="recaptcha" ')?> <?=lang('form:recaptcha')?>
		<!-- <?=form_radio($form_name_settings.'[type]', 'nucaptcha', ((isset($type) == TRUE && $type == 'nucaptcha') ? TRUE : FALSE), ' class="ShowHideSubmitBtn" rel="nucaptcha" ')?> <?=lang('form:nucaptcha')?> -->

		<p class="btn_recaptcha">
			<br />
			<label><?=lang('form:recaptcha_theme')?></label> <br />
			<?php
				$opts = array();
				$opts['red'] = 'Red';
				$opts['white'] = 'White';
				$opts['clean'] = 'Clean';
				$opts['blackglass'] = 'Blackglass';
			?>
			<?=form_dropdown($form_name_settings.'[recaptcha][theme]', $opts, ((isset($recaptcha['theme']) != FALSE) ? $recaptcha['theme'] : ''))?>
			<br />
			<label><?=lang('form:recaptcha_lang')?></label> <br />
			<?php
				$opts = array();
				$opts['en'] = 'English';
				$opts['nl'] = 'Dutch';
				$opts['fr'] = 'French';
				$opts['de'] = 'German';
				$opts['pt'] = 'Portuguese';
				$opts['ru'] = 'Russian';
				$opts['es'] = 'Spanish';
				$opts['tr'] = 'Turkish';
			?>
			<?=form_dropdown($form_name_settings.'[recaptcha][lang]', $opts, ((isset($recaptcha['lang']) != FALSE) ? $recaptcha['lang'] : ''))?>

		</p>

	</td>
</tr>