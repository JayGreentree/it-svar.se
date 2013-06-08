<?php echo $this->view('mcp/menu'); ?>

<div class="btitle" id="actionbar">
	<h2><?=lang('ci:regenerate_sizes')?></h2>

</div>

<div id="regensizes">
    <div class="sidebar">
        <h3><?=lang('ci:ci_fields')?></h3>
        <ul class="fields">
        	<?php foreach($fields as $field_id => $field_label):?>
        	<li><a href="#" class="ci_grab_images" data-id="<?=$field_id?>"><?=$field_label?></a></li>
        	<?php endforeach;?>
    	</ul>
    </div>
    <div class="fieldcontent"><div class="wrapper">

    	<div class="regenblock block-batches">
			<a href="#" class="abtn start" id="start_regen"><span><?=lang('ci:start_resize')?></span></a>
			<hr>
			<p class="noselect"><?=lang('ci:select_regen_field')?></p>
			<div class="batches"></div>
    	</div>

		<div class="regenblock block-table">

			<table cellpadding="0" cellspacing="0" border="0" class="dtable">
				<thead>
					<tr>
						<th>Image ID</th>
						<th><?=lang('ci:filename')?></th>
						<th><?=lang('ci:title')?></th>
						<th>Entry ID</th>
						<th><?=lang('ci:status')?></th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="9" class="nofiles">&nbsp;</td></tr>
				</tbody>
			</table>
		</div>

    </div></div>
</div>




<br clear="all">

<div id="error_log" style="padding:0 20px 20px;display:none">
	<div class="btitle"><h2><?=lang('error')?></h2></div>
	<div class="body" style="border:1px solid #ccc; padding:20px;"></div>
</div>
