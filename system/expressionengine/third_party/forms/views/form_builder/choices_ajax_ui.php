<div class="modal-header">
	<button class="close" data-dismiss="modal">×</button>
	<h3><?=lang('form:bulkadd')?></h3>
</div>
<div class="modal-body">
	<div id="FormsChoices">
		<div class="top"><?=lang('form:bulkchoice:exp')?></div>

		<div class="wrapper">
			<div class="left">
			<?php foreach($lists as $list_label => $list): ?>
				<a href="#"><?=$list_label?> <span><?=$list?></span></a>
			<?php endforeach; ?>
			</div>
			<div class="middle">&nbsp;</div>
			<div class="right">
				<textarea id="FormsChoicesText"></textarea>
				<small><?=lang('form:option_setting_ex')?></small>
			</div>
			<br clear="all"><br>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button href="#" class="btn" data-dismiss="modal"><?=lang('form:close')?></button>
	<button href="#" class="btn btn-primary"><?=lang('form:save');?></button>
</div>
