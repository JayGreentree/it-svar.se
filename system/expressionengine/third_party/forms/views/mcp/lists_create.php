<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="Lists">
	<div class="btitle" id="actionbar">
		<h2>
			<?php if ($list_id > 0):?> <?=lang('form:list_edit')?>
			<?php else:?> <?=lang('form:list_new')?> <?php endif;?>
		</h2>
	</div>

<div style="padding:20px;">
<?=form_open($base_url_short.AMP.'method=update_list')?>
<?=form_hidden('list_id', $list_id);?>

<div class="FormTable">
	<h2><?=lang('form:list_gen_info')?></h2>
	<table>
		<tbody>
			<tr>
				<td class="flabel"><?=lang('form:list_label')?></td>
				<td><?=form_input('list_label', $list_label)?></td>
			</tr>
			<tr>
				<td class="flabel">
					<?=lang('form:list:items')?>
					<br><br>
					<?=lang('form:option_setting_ex')?>
				</td>
				<td><textarea name="items" rows="30"><?=$items?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>

<input class="submit" type="submit" value="<?=lang('form:save')?>">
<?=form_close()?>
</div>

</div><!--fbody-->

<?php echo $this->view('mcp/_footer'); ?>