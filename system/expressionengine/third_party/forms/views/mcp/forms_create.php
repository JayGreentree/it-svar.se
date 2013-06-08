<?php echo $this->view('mcp/_header'); ?>

<div id="fbody" id="NewForm">
    <div class="btitle" id="actionbar">
        <h2><?=lang('form:builder')?></h2>
    </div>

<?=form_open($base_url_short.AMP.'method=update_form')?>

	<?=form_hidden('form_id', $form['form_id']);?>

	<div class="Forms" data-fieldid="0" data-formid="<?=$form['form_id']?>">
		<?=$this->load->view('form_builder/builder.php');?>
		<br clear="all">
		<div style="padding:10px">
			<input type="submit" class="submit" value="<?=lang('form:save')?>">
		</div>
	</div>

<?=form_close()?>
</div>


<?php echo $this->view('mcp/_footer'); ?>