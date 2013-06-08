<?php if (isset($dashboard) === FALSE || !$dashboard):?>
<?php echo $this->view('mcp/_header'); ?>
<?php endif;?>

<div class="fbody" id="FormsDT">

	<div id="leftbar">
		<h6><?=lang('form:filter_by')?></h6>
		<div id="leftbar-filters">
			<div class="filter">
				<?php include_once(APPPATH.'config/countries.php');?>
				<?php $countries = array('xx' => lang('form:unknown')) + $countries;?>
				<?=form_multiselect('filter[country][]', $countries, '', ' class="chzn-select" data-placeholder="'.lang('form:filter:country').'" style="width:97.7%;" ');?>
			</div>
			<div class="filter">
				<?=form_multiselect('filter[members][]', $members, '', ' class="chzn-select" data-placeholder="'.lang('form:filter:members').'" style="width:97.7%;" ');?>
			</div>
			<div class="filter">
				<input type="text" name="filter[date][from]" placeholder="<?=lang('form:date_from')?>" class="datepicker">
			</div>
			<div class="filter">
				<input type="text" name="filter[date][to]" placeholder="<?=lang('form:date_to')?>" class="datepicker">
			</div>
		</div>

		<h6><?=lang('form:toggle_fields')?></h6>
		<div id="leftbar-columns" class="sidebar-labels">
			<?php foreach ($standard_fields as $field_name => $field_title):?>
			<span class="label label-success" data-field="<?=$field_name?>"><?=$field_title?></span>
			<?php endforeach;?>

			<?php foreach ($dbfields as $field):?>
			<span class="label" data-field="field_id_<?=$field->field_id?>"><?=$field->title?></span>
			<?php endforeach;?>
		</div>
	</div>

	<div id="rightcontent" style="overflow-x:hidden">
		<div class="btitle" id="actionbar">
	        <h2><?=$form->form_title?></h2>
	        <a href="#" class="abtn pages ExportForm"><span><?=lang('form:export')?></span></a>
	        <a href="#" class="abtn fentry btn-action ViewEntry" data-multiple="no"><span><?=lang('form:view_entry')?></span></a>
	        <a href="#" class="abtn print btn-action PrintEntry" data-multiple="yes"><span><?=lang('form:print')?></span></a>
	        <a href="#" class="abtn delete btn-action DelEntry" data-multiple="yes"><span><?=lang('form:delete')?></span></a>
	        <span class="loadingblock"><?=lang('form:loading')?></span>
	    </div>

	    <div id="PageWrapper" style="position:relative; overflow:display">
			<div id="EntriesDT" style="padding:10px 0 10px 10px; position:relative; width:98%;">
				<table cellpadding="0" cellspacing="0" border="0" class="DFTable datatable selectable" data-multicheck="yes" data-name="form_entries" data-url="ajax_method=datatable_forms_entries&form_id=<?=$form->form_id?>" data-savestate="yes">
			        <thead>
			          <tr>
			            <th style="width:50px"><input type="checkbox" class="CheckAll">&nbsp;&nbsp;<?=lang('form:id')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			            <?php foreach ($standard_fields as $field_name => $field_title):?>
							<th><?=$field_title?></th>
						<?php endforeach;?>
						<?php foreach ($dbfields as $field):?>
							<th><?=$field->title?></th>
						<?php endforeach;?>
			          </tr>
			        </thead>
			        <tbody>

			        </tbody>
			        <tfoot>
			        </tfoot>
			      </table>
			</div>
			<div id="FormEntryWrapper" style="padding:10px 0 10px 10px; position:relative; top:0; left:100%; width:98%;">
				<a href="#" class="abtn back FormEntrySlideBack"><span><?=lang('form:go_back')?></span></a>
				<div id="fentry" style="padding:10px 0;"></div>
			</div>
			<br clear="all">
		</div>
	</div>

	<br clear="all">

</div><!--fbody-->


<?php if ($js_added === false): ?>

<script type="text/javascript">
Forms.DatatablesCols['form_entries'] = [];
Forms.DatatablesCols['form_entries'].push({mDataProp:'fentry_id', sName:'fentry_id', bSortable: false, sWidth:'50px'});

<?php foreach ($standard_fields as $field_name => $field_title):?>
Forms.DatatablesCols['form_entries'].push({mDataProp:'<?=$field_name?>', sName:'<?=$field_name?>'});
<?php endforeach;?>

<?php foreach ($dbfields as $field):?>
Forms.DatatablesCols['form_entries'].push({mDataProp:'field_id_<?=$field->field_id?>', sName:'field_id_<?=$field->field_id?>', bVisible:false});
<?php endforeach;?>

</script>
<?php endif;?>

<div id="del_fentry" class="modal fade" style="display:none">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3><?=lang('form:delete')?></h3>
    </div>
    <div class="modal-body">
    	<p></p>
    </div>
    <div class="modal-footer">
    	<button href="#" class="btn" data-dismiss="modal"><?=lang('form:close')?></button>
    	<button href="#" class="btn btn-primary"><?=lang('form:delete');?></button>
    </div>
</div>

<div id="print_fentry" class="modal fade" style="display:none">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3><?=lang('form:print_fentry')?></h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
    	<button href="#" class="btn" data-dismiss="modal"><?=lang('form:close')?></button>
    </div>
</div>

<div id="FormsExportDialog" class="modal fade" style="display:none">

	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3><?=lang('form:export')?></h3>
    </div>
    <div class="modal-body">
		<form method="POST">


		<div class="FormTable">
			<table>
				<tbody>
					<tr>
						<td class="flabel"><?=lang('form:export:format')?></td>
						<td>
							<input name="export[format]" type="radio" value="csv" checked onclick="$('#FormsExportDialog').find('.ExportSectionWrapper .section').hide().filter('.csv').slideDown();"> CSV&nbsp;&nbsp;&nbsp;
							<input name="export[format]" type="radio" value="xls" onclick="$('#FormsExportDialog').find('.ExportSectionWrapper .section').hide().filter('.xls').slideDown();"> XLS
							<input name="export[form_id]" type="hidden" value="<?=$form->form_id?>">
						</td>
					</tr>
					<tr>
						<td class="flabel"><?=lang('form:export:fields')?></td>
						<td>
							<input name="export[fields]" type="radio" value="current" checked> <?=lang('form:export:current_fields')?> <br />
							<input name="export[fields]" type="radio" value="all"> <?=lang('form:export:all_fields')?>
						</td>
					</tr>
					<tr>
						<td class="flabel"><?=lang('form:export:entries')?></td>
						<td>
							<input name="export[entries]" type="radio" value="current" checked> <?=lang('form:export:current_entries')?> <br />
							<input name="export[entries]" type="radio" value="all"> <?=lang('form:export:all_entries')?>
						</td>
					</tr>
					<tr>
						<td class="flabel"><?=lang('form:export:include_header')?></td>
						<td>
							<input name="export[include_header]" type="radio" value="yes" checked> <?=lang('form:yes')?> &nbsp;
							<input name="export[include_header]" type="radio" value="no"> <?=lang('form:no')?><br />
						</td>
					</tr>

					<tr>
						<td class="flabel"><?=lang('form:export:member_info')?></td>
						<td>
							<input name="export[member_info]" type="radio" value="screen_name" checked> <?=lang('form:export:screen_name')?> <br />
							<input name="export[member_info]" type="radio" value="username"> <?=lang('form:export:username')?><br />
							<input name="export[member_info]" type="radio" value="email"> <?=lang('form:export:email')?><br />
							<input name="export[member_info]" type="radio" value="member_id"> <?=lang('form:export:member_id')?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>


		<div class="FormTable ExportSectionWrapper">
			<table>
				<tbody class="section csv">
					<tr>
						<td class="flabel"><?=lang('form:export:delimiter')?></td>
						<td>
							<input name="export[delimiter]" type="radio" value="comma" checked> <?=lang('form:export:comma')?> <br />
							<input name="export[delimiter]" type="radio" value="tab"> <?=lang('form:export:tabs')?><br />
							<input name="export[delimiter]" type="radio" value="semicolon"> <?=lang('form:export:scolons')?> <br />
							<input name="export[delimiter]" type="radio" value="pipe"> <?=lang('form:export:pipes')?>
						</td>
					</tr>
					<tr>
						<td class="flabel"><?=lang('form:export:enclosure')?></td>
						<td>
							<input name="export[enclosure]" type="radio" value="double_quote" checked> <?=lang('form:export:dblquote')?> &nbsp;
							<input name="export[enclosure]" type="radio" value="quote"> <?=lang('form:export:quote')?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="hidden_fields"></div></form>
	</div>


	<div class="modal-footer">
    	<button href="#" class="btn" data-dismiss="modal"><?=lang('form:close')?></button>
    	<button href="#" class="btn btn-primary" data-normal="<?=lang('form:export');?>" data-loading="<?=lang('form:export:loading')?>"><?=lang('form:export');?></button>
    </div>
</div>

<?php if (isset($dashboard) === FALSE || !$dashboard):?>
<?php echo $this->view('mcp/_footer') ?>
<?php endif;?>
