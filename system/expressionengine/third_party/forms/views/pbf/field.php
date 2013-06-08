<div class="Forms" data-formid="<?=$form['form_id']?>">
<?php if ($missing_settings == TRUE) exit('<strong style="color:red">'.lang('form:missing_settings').'</strong>');?>

<?=$this->load->view('form_builder/builder.php');?>


</div>
