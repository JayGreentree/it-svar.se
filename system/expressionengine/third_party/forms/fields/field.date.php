<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms DATE field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_date extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Date',
		'name' 		=>	'date',
		'category'	=>	'power_tools',
		'version'	=>	'1.0',
	);

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->default_settings['date_input_type'] = 'datepicker';
		$this->default_settings['datepicker_format'] = 'american';
		$this->default_settings['date_format'] = 'd/m/Y';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$field['settings'] = array_merge($this->default_settings, $field['settings']);

		$options = array();
		$options['name'] = $field['form_name'];
		$options['value'] = '';
		$options['class'] = 'text ';
		$options['id'] = $field['form_elem_id'];

		// -----------------------------------------
		// If in publish field, lets disable it
		// -----------------------------------------
		if ($template == FALSE)
		{
			$options['readonly'] = 'readonly';
			$options['name'] = '';
		}

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{

				$options['class'] .= 'required validate[required] ';
			}
		}

		$out  = '';

		// -----------------------------------------
		// Datepicker?
		// -----------------------------------------
		if ($field['settings']['date_input_type'] == 'datepicker')
		{
			$dateformat="mm/dd/yy";
			if (isset($field['settings']['datepicker_format']) == TRUE && $field['settings']['datepicker_format'] == 'european')
			{
				$dateformat="dd/mm/yy";
			}

			$field_id_str = (isset($field['field_id']) == TRUE && $field['field_id'] > 0) ? '#forms_field_'.$field['field_id'] : '';

			if ($data != FALSE) $options['value'] = $data;
			$options['class'] .= ' formsfdatepicker ';
			$out .= form_input($options);
			$out .= $this->EE->forms_helper->output_js_buffer("
			<script type='text/javascript'>
			$(function() {
				jQuery('{$field_id_str} input.formsfdatepicker').datepicker({dateFormat:'{$dateformat}'});
			});
			</script>");

			return $out;
		}

		// -----------------------------------------
		// Date Field
		// -----------------------------------------
		if ($field['settings']['date_input_type'] == 'datefield')
		{
			$out .= '<div class="dfinput_dates">';
			$options['maxlength'] = '2';

			if (isset($field['settings']['datepicker_format']) == TRUE && $field['settings']['datepicker_format'] == 'european')
			{
				$out .= '<div class="df_date_elem">';
				$options['name'] = $field['form_name'].'[day]';
				$options['id'] = $field['form_elem_id'].'_day';
				$options['type'] = 'number';
				$options['min'] = 1;
				$options['max'] = 31;
				if (isset($data['day'])) $options['value'] = $data['day'];
				$out .= form_input($options);
				$out .= 	'<label for="'.$field['form_elem_id'].'_day'.'">' . $this->EE->lang->line('form:day') . '</label>';
				$out .= '</div>';

				$out .= '<div class="df_date_elem">';
				$options['name'] = $field['form_name'].'[month]';
				$options['id'] = $field['form_elem_id'].'_month';
				$options['type'] = 'number';
				$options['min'] = 1;
				$options['max'] = 24;
				if (isset($data['month'])) $options['value'] = $data['month'];
				$out .= form_input($options);
				$out .= 	'<label for="'.$field['form_elem_id'].'_month'.'">' . $this->EE->lang->line('form:month') . '</label>';
				$out .= '</div>';
			}
			else
			{

				$out .= '<div class="df_date_elem">';
				$options['name'] = $field['form_name'].'[month]';
				$options['id'] = $field['form_elem_id'].'_month';
				$options['type'] = 'number';
				$options['min'] = 1;
				$options['max'] = 24;
				if (isset($data['month'])) $options['value'] = $data['month'];
				$out .= form_input($options);
				$out .= 	'<label for="'.$field['form_elem_id'].'_month'.'">' . $this->EE->lang->line('form:month') . '</label>';
				$out .= '</div>';

				$out .= '<div class="df_date_elem">';
				$options['name'] = $field['form_name'].'[day]';
				$options['id'] = $field['form_elem_id'].'_day';
				$options['type'] = 'number';
				$options['min'] = 1;
				$options['max'] = 31;
				if (isset($data['day'])) $options['value'] = $data['day'];
				$out .= form_input($options);
				$out .= 	'<label for="'.$field['form_elem_id'].'_day'.'">' . $this->EE->lang->line('form:day') . '</label>';
				$out .= '</div>';
			}

			$out .= '<div class="df_date_elem">';
			$options['maxlength'] = '4';
			$options['name'] = $field['form_name'].'[year]';
			$options['id'] = $field['form_elem_id'].'_year';
			$options['type'] = 'number';
			if (isset($data['year'])) $options['value'] = $data['year'];
			$out .= form_input($options);
			$out .= 	'<label for="'.$field['form_elem_id'].'_year'.'">' . $this->EE->lang->line('form:year') . '</label>';
			$out .= '</div>';

			$out .= '<br clear="all">';
			$out .= '</div>';
		}

		// -----------------------------------------
		// Date Drop Down
		// -----------------------------------------
		if ($field['settings']['date_input_type'] == 'dateselect')
		{
			$css_class = '';
			// -----------------------------------------
			// Add JS Validation support
			// -----------------------------------------
			if ($template == TRUE)
			{
				if ($field['required'] == TRUE)
				{

					$css_class .= 'required validate[required] ';
				}
			}

			$out .= '<div class="dfinput_dates">';

			if (isset($field['settings']['datepicker_format']) == TRUE && $field['settings']['datepicker_format'] == 'european')
			{
				$out .= '<div class="df_date_elem">';
				$options['name'] = $field['form_name'].'[day]';
				$options['value'] = isset($data['day']) ? $data['day'] : '';
				$out .= form_dropdown($options['name'], array(''=>'',1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31), $options['value'], ' class="'.$css_class.'" id="'.$field['form_elem_id'].'_day'.'" ');
				$out .= 	'<label for="'.$field['form_elem_id'].'_day'.'">' . $this->EE->lang->line('form:day') . '</label>';
				$out .= '</div>';

				$out .= '<div class="df_date_elem_extra">';
				$options['name'] = $field['form_name'].'[month]';
				$options['value'] = isset($data['month']) ? $data['month'] : '';
				$items = array('' => '', '01' => $this->EE->lang->line('January'), '02' => $this->EE->lang->line('February'), '03' => $this->EE->lang->line('March'), '04' => $this->EE->lang->line('April'), '05' => $this->EE->lang->line('May_l'), '06' => $this->EE->lang->line('June'), '07' => $this->EE->lang->line('July'), '08' => $this->EE->lang->line('August'), '09' => $this->EE->lang->line('September'), '10' => $this->EE->lang->line('October'), '11' => $this->EE->lang->line('November'), '12' => $this->EE->lang->line('December'));
				$out .= form_dropdown($options['name'], $items, $options['value'], ' class="'.$css_class.'" id="'.$field['form_elem_id'].'_month'.'" ');
				$out .= 	'<label for="'.$field['form_elem_id'].'_month'.'">' . $this->EE->lang->line('form:month') . '</label>';
				$out .= '</div>';
			}
			else
			{
				$out .= '<div class="df_date_elem_extra">';
				$options['name'] = $field['form_name'].'[month]';
				$options['value'] = isset($data['month']) ? $data['month'] : '';
				$items = array('' => '', '01' => $this->EE->lang->line('January'), '02' => $this->EE->lang->line('February'), '03' => $this->EE->lang->line('March'), '04' => $this->EE->lang->line('April'), '05' => $this->EE->lang->line('May_l'), '06' => $this->EE->lang->line('June'), '07' => $this->EE->lang->line('July'), '08' => $this->EE->lang->line('August'), '09' => $this->EE->lang->line('September'), '10' => $this->EE->lang->line('October'), '11' => $this->EE->lang->line('November'), '12' => $this->EE->lang->line('December'));
				$out .= form_dropdown($options['name'], $items, $options['value'], ' class="'.$css_class.'" id="'.$field['form_elem_id'].'_month'.'" ');
				$out .= 	'<label for="'.$field['form_elem_id'].'_month'.'">' . $this->EE->lang->line('form:month') . '</label>';
				$out .= '</div>';

				$out .= '<div class="df_date_elem">';
				$options['name'] = $field['form_name'].'[day]';
				$options['value'] = isset($data['day']) ? $data['day'] : '';
				$out .= form_dropdown($options['name'], array(''=>'',1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31), $options['value'], ' class="'.$css_class.'" id="'.$field['form_elem_id'].'_day'.'" ');
				$out .= 	'<label for="'.$field['form_elem_id'].'_day'.'">' . $this->EE->lang->line('form:day') . '</label>';
				$out .= '</div>';
			}

			$out .= '<div class="df_date_elem">';
			$options['name'] = $field['form_name'].'[year]';
			$options['value'] = isset($data['year']) ? $data['year'] : '';
			$items = range((date('Y')+5), 1900);
			$items = array_reverse($items, true);
			$items[''] = ' ';
    		$items = array_reverse($items, true);
    		$years = array();
    		foreach ($items as $item) $years[$item] = $item;
			$out .= form_dropdown($options['name'], $years, $options['value'], ' class="'.$css_class.'" id="'.$field['form_elem_id'].'_year'.'"  ');
			$out .= 	'<label for="'.$field['form_elem_id'].'_year'.'">' . $this->EE->lang->line('form:year') . '</label>';
			$out .= '</div>';

			$out .= '<br clear="all">';
			$out .= '</div>';
		}


		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		$field['settings'] = array_merge($this->default_settings, $field['settings']);

		if ($field['settings']['date_input_type'] == 'datefield' || $field['settings']['date_input_type'] == 'dateselect')
		{
			// Prepare the error
			$error = array('type' => 'general', 'msg' => $this->EE->lang->line('form:error:invalid_date'));

			// Day
			if (trim($data['day']) == FALSE)
			{
				return $error;
			}

			// Month
			if (trim($data['month']) == FALSE)
			{
				return $error;
			}

			// Year
			if (trim($data['year']) == FALSE)
			{
				return $error;
			}
		}


		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		if ($data == FALSE) $data = array();

		if (is_array($data) === FALSE)
		{
			$data = explode('/', $data);

			if ($settings['datepicker_format'] == 'european')
			{
				$data = array('day' => $data[0], 'month' => $data[1], 'year' => $data[2]);
			}
			else
			{
				$data = array('day' => $data[1], 'month' => $data[0], 'year' => $data[2]);
			}
		}

		return serialize($data);
	}

	// ********************************************************************************* //

	public function output_data($field=array(), $data, $type='html')
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		if ($data == FALSE) return;

		// -----------------------------------------
		// Data
		// -----------------------------------------
		$data = @unserialize($data);

		if (empty($data)) return;

		$data = "{$data['year']}-{$data['month']}-{$data['day']}";
		$date = strtotime($data);

		// -----------------------------------------
		// What Date Format?
		// -----------------------------------------
		$date_format="d/m/Y";
		if (isset($settings['date_format']) == TRUE) $date_format = $field['settings']['date_format'];

		// Return!
		return date($date_format, $date);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);

		return $this->EE->load->view('fields/date', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.date.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.date.php */
