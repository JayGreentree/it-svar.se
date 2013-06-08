<?php if ($skipped): ?>
	<div class="low-alertbox">
		<p><?=lang('low_variables_saved_except')?></p>
		<ul><?php foreach($skipped AS $row): ?>
			<li><?=($row['variable_label']?$row['variable_label']:$row['variable_name'])?></li>
		<?php endforeach; ?></ul>
	</div>
<?php endif; ?>

<form method="post" action="<?=$base_url.AMP?>method=save" enctype="multipart/form-data" id="low-variables-form">
	<div>
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />
		<input type="hidden" name="all_ids" value="<?=$all_ids?>" />
		<input type="hidden" name="group_id" value="<?=$group_id?>" />
	</div>

<?php if ($show_groups): ?>

	<div id="low-grouplist"<?php if ($is_manager): ?> class="low-manager ee2"<?php endif; ?>>
		<table class="mainTable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th scope="col"><?=lang('groups')?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<td>
						<ul id="low-sortable-groups">
							<?php foreach ($groups AS $gid => $row): if ($gid == 0) continue; ?>
								<li<?php if ($group_id == $gid): ?> class="active"<?php endif; ?>>
									<?php if ($is_manager): ?>
										<a href="<?=sprintf($del_group_url, $gid)?>" class="low-delete"
											title="<?=lang('delete_group').' '.$row['group_label']?>"><?=lang('delete_group')?></a>
										<a href="<?=sprintf($edit_group_url, $gid, 'home')?>" class="low-edit"
											title="<?=lang('edit_group').' '.$row['group_label']?>"><?=lang('edit_group')?></a>
										<span class="low-handle"></span>
									<?php endif; ?>
									<?php if ($row['var_count'] == 0): ?>
										<span class="low-grouplink" data-groupid="<?=$gid?>"><?=$row['group_label']?></span>
									<?php else: ?>
										<a class="low-grouplink" href="<?=sprintf($show_group_url, $gid)?>" data-groupid="<?=$gid?>">
										<?=$row['group_label']?> <small>(<?=$row['var_count']?>)</small></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
						<ul>
							<?php if (isset($groups[0])): ?>
								<li<?php if ($group_id === '0'): ?> class="active"<?php endif; ?>>
									<?php if ($is_manager): ?>
										<a href="<?=sprintf($edit_group_url, 0, 'home')?>" class="low-edit"
											title="<?=lang('edit_group').' '.$groups[0]['group_label']?>"><?=lang('edit_group')?></a>
									<?php endif; ?>
									<a href="<?=sprintf($show_group_url, 0)?>" class="low-grouplink"><?=$groups[0]['group_label']?>
									<small>(<?=$groups[0]['var_count']?>)</small></a>
								</li>
							<?php endif; ?>
							<?php if (count($groups) > 1): ?>
								<li<?php if ($group_id == 'all'): ?> class="active"<?php endif; ?>>
									<a href="<?=sprintf($show_group_url, 'all')?>" class="low-grouplink"><?=lang('show_all')?></a>
								</li>
							<?php endif; ?>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div id="low-varlist">

<?php endif; ?>

		<?php foreach ($group_ids AS $i => $gid): ?>
			<table class="mainTable low-vargroup" cellspacing="0" cellpadding="0" id="group-<?=$gid?>">
				<colgroup>
					<col class="label" />
					<col class="input" />
				</colgroup>
				<thead>
					<tr>
						<?php if ($show_groups): ?>
							<th scope="col" colspan="2"<?php if ( ! $i): ?> id="first-group"<?php endif; ?>>
								<?=$groups[$gid]['group_label']?>
							</th>
						<?php else: ?>
							<th scope="col"><?=lang('variable_name')?></th>
							<th scope="col" ><?=lang('variable_data')?></th>
						<?php endif; ?>
					</tr>
					<?php if ($groups[$gid]['group_notes']): ?>
						<tr>
							<td class="low-group-notes" colspan="2"><?=$groups[$gid]['group_notes']?></td>
						</tr>
					<?php endif; ?>
				<tbody>
				<?php if ( ! empty($vars[$gid])): ?>
					<?php foreach ($vars[$gid] AS $row): ?>
						<?php if ($row['variable_header']): ?>
							<tr>
								<td class="low-group-notes" colspan="2"><?=$row['variable_header']?></td>
							</tr>
						<?php endif; ?>
						<tr class="<?=low_zebra()?><?php if ( ! empty($row['error_msg'])): ?> low-var-alert<?php endif; ?>">
							<td style="vertical-align:top">
								<?php if ($is_manager): ?>
									<a href="<?=sprintf($edit_var_url, $row['variable_id'], $gid)?>" class="edit-var"
										title="<?=lang('manage_this_variable')?>"><?=lang('manage_this_variable')?></a>
								<?php endif; ?>

								<div class="low-label-holder<?php if ($is_manager): ?> low-manager<?php endif; ?>">
									<strong class="low-label"><?=$row['variable_label']?></strong>
									<code><?=$row['variable_name']?></code>
								</div>

								<?php if ( ! empty($row['error_msg'])): ?>
									<div class="low-var-alert">
										<?=(is_array($row['error_msg']) ? implode('<br />', $row['error_msg']) : lang($row['error_msg']))?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty($row['variable_notes'])): ?>
									<div class="low-var-notes"><?=$row['variable_notes']?></div>
								<?php endif; ?>
							</td>
							<td><?=$row['variable_input']?></td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="2">No variables found in this group.</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		<?php endforeach; ?>

		<button type="submit" class="submit"><?=lang('low_variables_save')?></button>

		<?php if (in_array($clear_cache, array('opt_in', 'opt_out'))): ?>
			<label style="margin-left:1.5em;font-weight:normal;cursor:pointer">
				<input type="checkbox" name="clear_cache" value="y"<?php if ($clear_cache == 'opt_out'): ?> checked="checked"<?php endif; ?> />
				<?=lang('clear_cache')?>
			</label>
		<?php endif; ?>

<?php if ($show_groups): ?></div><?php endif; ?>

</form>
