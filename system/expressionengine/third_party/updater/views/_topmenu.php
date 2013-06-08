<!--[if IE]> <div id="updater" class="updater-ie"> <![endif]-->
<!--[if !IE]><!--> <div id="updater"><!--<![endif]-->

<div id="umenu">
	<ul>
		<li class="<?=(($section == 'home')) ? ' current': ''?>"><a class="home" href="<?=$base_url?>&method=home"><?=lang('u:dashboard')?></a></li>
		<li class="<?=(($section == 'settings')) ? ' current': ''?>"><a class="settings" href="<?=$base_url?>&method=settings"><?=lang('settings')?></a></li>
	</ul>
</div>

<div id="test_ajax_error" class="alert alert-error alert-block" style="margin:0px;padding:10px;display:none">
    <h4><?=lang('u:warning')?></h4>
    <?=lang('error:test_ajax_failed')?>
    <div class="error">
        <div class="inner"></div>

        <a href="#" class="js-show_error"><strong>Show Response</strong></a>
        <textarea style="display:none"></textarea>
    </div>
</div>


<div class="updater-body">
