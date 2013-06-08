<div class="dmenu">
	<ul>
		<li class="<?=(($section == 'editors')) ? ' current': ''?>"><a class="editors" href="<?=$base_url?>&method=index"><?=lang('ed:editor_conf')?></a></li>
		<li class="<?=(($section == 'categories')) ? ' current': ''?>"><a class="categories" href="<?=$base_url?>&method=categories"><?=lang('ed:category_settings')?></a></li>
        <li class="<?=(($section == 'buttons')) ? ' current': ''?>"><a class="buttons" href="<?=$base_url?>&method=buttons"><?=lang('ed:buttons')?></a></li>
	</ul>
</div>
