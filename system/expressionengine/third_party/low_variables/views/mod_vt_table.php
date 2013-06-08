<table class="low-table" id="low-table-<?=$var_id?>">
	<thead>
		<tr><?php foreach ($columns AS $col): ?>
			<th scope="col"><?=htmlspecialchars($col)?></th><?php endforeach; ?>
		</tr>
	</thead>
	<tbody><?php foreach ($rows AS $rownum => $row): ?>
		<tr><?php foreach (array_keys($columns) AS $i): ?>
			<td><?=htmlspecialchars(isset($row[$i]) ? $row[$i] : '')?></td><?php endforeach; ?>
		</tr><?php endforeach; ?>
	</tbody>
</table>

<!--<?=$encoded_rows?>-->