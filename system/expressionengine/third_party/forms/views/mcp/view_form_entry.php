<table cellpadding="0" cellspacing="0" border="0" class="DFTable">
	<thead>
		<tr>
			<th><?=lang('form:date')?></th>
			<td><?=$this->forms_helper->formatDate('%d-%M-%Y %g:%i %A', $fentry['date'], true)?></td>
			<th><?=lang('form:country')?></th>
			<td><?=strtoupper($fentry['country'])?></td>
		</tr>
		<tr>
			<th><?=lang('form:member')?></th>
			<td><?=$fentry['screen_name']?> (<?=$fentry['email']?>)</td>
			<th><?=lang('form:ip')?></th>
			<td><?=long2ip($fentry['ip_address'])?></td>
		</tr>
	</thead>
</table>

<br />

<table cellpadding="0" cellspacing="0" border="0" class="DFTable">
	<thead>
	<?php foreach($dbfields[0] as $key => $field):?>
		<tr>
			<th style="width:175px"><?=$field['title']?></th>
			<td><?=$this->formsfields[ $field['field_type'] ]->output_data($field, $fentry['fid_'.$field['field_id']], 'html')?></td>
		<?php if (isset($dbfields[1][$key]) != FALSE):?>
			<th style="width:175px"><?=$dbfields[1][$key]['title']?></th>
			<td><?=$this->formsfields[ $dbfields[1][$key]['field_type'] ]->output_data($dbfields[1][$key], $fentry['fid_'.$dbfields[1][$key]['field_id']], 'html')?></td>
		<?php else:?>
			<th style="width:175px"></th>
			<td></td>
		<?php endif;?>
		</tr>
	<?php endforeach;?>
	</thead>
</table>
<br clear="all">

<?php if (empty($debug) === false):?>

<table cellpadding="0" cellspacing="0" border="0" class="DFTable" style="width:48%; float:left;">
<thead>
	<tr>
		<th>DEBUG: Admin Email</th>
	</tr>
</thead>
<tbody>
	<tr><td>
		<?php if (isset($debug['email']['admin']) === TRUE):?>
		<?=$debug['email']['admin']?>
		<?php else:?>
		No email was sent..
		<?php endif;?>
	</td></tr>
</tbody>
</table>

<table cellpadding="0" cellspacing="0" border="0" class="DFTable" style="width:48%; float:right;">
<thead>
	<tr>
		<th>DEBUG: User Email</th>
	</tr>
</thead>
<tbody>
	<tr><td>
		<?php if (isset($debug['email']['user']) === TRUE):?>
		<?=$debug['email']['user']?>
		<?php else:?>
		No email was sent..
		<?php endif;?>
	</td></tr>
</tbody>
</table>

<br clear="all">
<?php endif;?>
