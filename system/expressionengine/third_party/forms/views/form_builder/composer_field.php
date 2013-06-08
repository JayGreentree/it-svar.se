<div class="FormElem" data-fieldtype="<?=$field_type?>" data-fieldid="<?=$field_id?>" data-fieldhash="<?=$field_hash?>" data-fieldlabel="<?=$title?>">
<div class="inner"><?=$field_body?></div>
<div class="tools">
	<a href="#" class="toggle_settings"></a>
	<a href="#" class="move"></a>
	<a href="#" class="remove"></a>
</div>

<?php // &nbsp; chars break! that's why the base64_encode()?>
<textarea name="<?=$form_name?>[fields][]" class="settings"><?=base64_encode($temp_field)?></textarea>
<div style="display:none" class="settingshtml"><?=base64_encode($settings_body)?></div>
</div>