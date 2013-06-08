<form action="<?=$base_url?>&amp;method=delete" method="post">
	<div>
		<input type="hidden" name="variable_id" value="<?=$variable_ids?>" />
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />
	</div>
	<div id="low-variables-delete">
		<p><?=$confirm_message?></p>
		<ul><?php foreach($variable_names AS $name): ?>
			<li><code><?=htmlspecialchars($name)?></code></li>
		<?php endforeach; ?></ul>
		<p class="important"><?=lang('action_can_not_be_undone')?></p>

		<button type="submit" class="submit"><?=lang('delete')?></button>
		<a href="<?=$base_url?>&amp;method=manage" class="cancel"><?=lang('cancel')?></a>
	</div>
</form>