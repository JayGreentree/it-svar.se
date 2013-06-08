<?php echo $this->view('menu'); ?>
<div class="dtable" style="margin:0">
	<h2 style="padding:9px 9px 5px">
		<?=lang('ed:new_conf')?>
	</h2>
</div>

<div id="dbody">

<?=form_open($base_url_short.AMP.'method=update_configuration')?>
<?=form_hidden('config_id', $config_id);?>

<div class="dtable">
	<table>
		<tbody>
			<tr>
				<td><label><?=lang('ed:name')?></label></td>
				<td>
					<input name="config_label" type="text" value="<?=$config_label?>">
				</td>
			</tr>
		</tbody>
	</table>
</div>


<?php echo $this->view('editor_settings'); ?>

<input name="submit" value="Save" class="submit" type="submit">
<?=form_close()?>


</div>
