<?php echo $this->view('menu'); ?>
<div class="dtable" style="margin:0">
	<h2 style="padding:9px 9px 5px">
		<?=lang('ed:category_settings')?>
	</h2>
</div>

<div id="dbody">

<?=form_open($base_url_short.AMP.'method=update_categories')?>


<?php echo $this->view('editor_settings'); ?>

<input name="submit" value="<?=lang('ed:save')?>" class="submit" type="submit">
<?=form_close()?>


</div>
