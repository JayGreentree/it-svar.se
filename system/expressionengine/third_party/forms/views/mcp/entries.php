<?php echo $this->view('mcp/_header'); ?>

<div class="fbody" id="FormsEntries">

	<div id="leftbar">
		<h6><?=lang('form:filter_by')?></h6>
		<div id="leftbar-filters">
			<div class="filter">
				<?=form_multiselect('filter[forms][]', $forms, '', ' class="chzn-select" data-placeholder="'.lang('form:filter:form').'" style="width:97.7%;" ');?>
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
			<div class="filter">
				<?php include_once(APPPATH.'config/countries.php');?>
				<?php $countries = array('xx' => lang('form:unknown')) + $countries;?>
				<?=form_multiselect('filter[country][]', $countries, '', ' class="chzn-select" data-placeholder="'.lang('form:filter:country').'" style="width:97.7%;" ');?>
			</div>
		</div>
	</div>

	<div id="rightcontent" style="overflow-x:hidden">
		<div class="btitle" id="actionbar">
	        <h2><?=lang('form:submissions')?></h2>
	        <a href="#" class="abtn print btn-action PrintEntry" data-multiple="yes"><span><?=lang('form:print')?></span></a>
	        <a href="#" class="abtn fentry btn-action ViewEntry" data-multiple="no"><span><?=lang('form:view_entry')?></span></a>
	        <a href="#" class="abtn delete btn-action DelEntry" data-multiple="yes"><span><?=lang('form:delete')?></span></a>
	        <span class="loadingblock"><?=lang('form:loading')?></span>
	    </div>

	    <div id="PageWrapper" style="position:relative; overflow:display">
			<div id="EntriesDT" style="padding:10px 0 10px 10px; position:relative; width:98%;">
				<table cellpadding="0" cellspacing="0" border="0" class="DFTable datatable selectable" data-multicheck="yes" data-name="submissions" data-url="ajax_method=datatable_submissions" data-savestate="no">
			        <thead>
			          <tr>
			            <th style="width:50px"><input type="checkbox" class="CheckAll">&nbsp;&nbsp;<?=lang('form:id')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			            <?php foreach ($standard_fields as $field_name => $field_title):?>
							<th><?=$field_title?></th>
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



</div>




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


<?php echo $this->view('mcp/_footer') ?>
