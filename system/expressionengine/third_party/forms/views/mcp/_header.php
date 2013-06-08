<script type="text/javascript">
jQuery(document).ready(function(){
    Forms.mcp_init();
});
</script>

<div id="fmenu">
	<ul>
		<!-- <li class="<?=(($PageHeader == 'home')) ? ' current': ''?>"><a class="home" href="<?=$base_url?>"><?=lang('form:home')?></a></li> -->
		<li class="<?=(($PageHeader == 'forms')) ? ' current': ''?>"><a class="forms" href="<?=$base_url?>&method=forms"><?=lang('forms')?></a></li>
		<li class="<?=(($PageHeader == 'entries')) ? ' current': ''?>"><a class="entries" href="<?=$base_url?>&method=entries"><?=lang('form:submissions')?></a></li>
		<li class="<?=(($PageHeader == 'templates')) ? ' current': ''?>"><a class="templates" href="<?=$base_url?>&method=templates"><?=lang('form:templates')?></a></li>
		<li class="<?=(($PageHeader == 'lists')) ? ' current': ''?>"><a class="lists" href="<?=$base_url?>&method=lists"><?=lang('form:lists')?></a></li>
		<li class="<?=(($PageHeader == 'settings')) ? ' current': ''?>"><a class="settings" href="<?=$base_url?>&method=settings"><?=lang('form:settings')?></a></li>
	</ul>
</div>

<?php if ($this->input->get('method') != 'create_form'):?>
<div id="fbody">
<?php endif;?>
