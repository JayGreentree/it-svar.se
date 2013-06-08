<table class="low-table" id="low-table-<?=$var_id?>" cellspacing="0" cellpadding="0">
	<colgroup>
		<?php for ($i = 0; $i < $col_count; $i++): ?>
			<col style="width:<?=(100/$col_count)?>%" />
		<?php endfor; ?>
	</colgroup>
	<thead>
		<tr>
			<?php foreach ($columns AS $col): ?>
				<th scope="col"><?=htmlspecialchars($col)?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="<?=$col_count?>"><a href="#" class="low-add"><b>+</b> <?=lang('add_row')?></a></td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach ($rows AS $rownum => $row): ?>
			<tr>
				<?php foreach (array_keys($columns) AS $i): ?>
					<td>
						<input type="text" name="var[<?=$var_id?>][<?=$rownum?>][<?=$i?>]" value="<?=htmlspecialchars(isset($row[$i]) ? $row[$i] : '')?>" />
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
