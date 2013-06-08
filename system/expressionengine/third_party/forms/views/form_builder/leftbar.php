<input class="filter_fields" type="input" placeholder="<?=lang('form:filter_fields')?>">

<?php foreach ($form['fields'] as $cat => $catfields):?>
	<h6><?=lang('form:'.$cat)?></h6>
	<div class="sort">
	<?php foreach($catfields as $class_name): ?>
		<?php if (isset($this->formsfields[$class_name]) == FALSE) continue;?>
		<span class="label label-info draggable field_<?=$class_name?>" data-field="<?=$class_name?>">
			<img src="<?=FORMS_THEME_URL?>fields/<?=$class_name?>.png" class="move"/>
			<?=$this->formsfields[$class_name]->info['title']?>
		</span>
	<?php endforeach;?>
	</div>
<?php endforeach;?>