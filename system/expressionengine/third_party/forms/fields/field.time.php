<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms TIME field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_time extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Time',
		'name' 		=>	'time',
		'category'	=>	'power_tools',
		'version'	=>	'1.0',
	);


	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$options = array();
		$options['name'] = $field['form_name'];
		$options['maxlength'] = '2';
		$css_class = 'text ';
		$out ='';

		// -----------------------------------------
		// If in publish field, lets disable it
		// -----------------------------------------
		if ($template == FALSE)
		{
			$options['readonly'] = 'readonly';
			$options['name'] = '';
		}

		// -----------------------------------------
		// Default Settings
		// -----------------------------------------
		if (isset($field['settings']['time_format']) == FALSE) $field['settings']['time_format'] = '12h';

		// -----------------------------------------
		// Render
		// -----------------------------------------

		$out .= '<div class="dfinput_times">';

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$css_class .= ' required validate[required,custom[number],min[0],max[24]] ';
			}
		}

		$out .= '<div class="df_time_elem">';
		$options['class'] = $css_class;
		$options['name'] = $field['form_name'].'[hour]';
		$options['id'] = $field['form_elem_id'].'_hour';
		$options['min'] = 0;
		if (isset($field['settings']['time_format']) == TRUE && $field['settings']['time_format'] == '12h') {
			$options['max'] = 12;
		} else {
			$options['max'] = 24;
		}
		$options['type'] = 'number';
		if (isset($data['hour'])) $options['value'] = $data['hour'];
		$out .= form_input($options);
		$out .= 	'<label for="'.$field['form_elem_id'].'_hour'.'">' . $this->EE->lang->line('form:hour') . '</label>';
		$out .= '</div>';


		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$css_class .= ' required validate[required,custom[number],min[0],max[60]] ';
			}
		}

		$out .= '<div class="df_time_elem">';
		$options['class'] = $css_class;
		$options['name'] = $field['form_name'].'[minute]';
		$options['id'] = $field['form_elem_id'].'_min';
		$options['type'] = 'number';
		$options['min'] = 0;
		$options['max'] = 60;

		if (isset($data['minute'])) $options['value'] = $data['minute'];
		$out .= form_input($options);
		$out .= 	'<label for="'.$field['form_elem_id'].'_min'.'">' . $this->EE->lang->line('form:minute') . '</label>';
		$out .= '</div>';

		if (isset($field['settings']['time_format']) == TRUE && $field['settings']['time_format'] == '12h')
		{
			$out .= '<div class="df_time_elem">';
			$options['name'] = $field['form_name'].'[ampm]';
			$value = (isset($data['ampm'])) ? $data['ampm'] : '';
			$out .= form_dropdown($options['name'], array('AM' => 'AM', 'PM' => 'PM'), $value, ' id="'.$field['form_elem_id'].'_ampm'.'" ');
			$out .= '</div>';
		}

		$out .= '<br clear="all">';
		$out .= '</div>';

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		$hour = (isset($data['hour'])) ? $data['hour'] : '';
		$minute = (isset($data['minute'])) ? $data['minute'] : '';

		// If both are empty, return TRUE
		if ($hour == FALSE && $minute == FALSE) return TRUE;

		if ($hour == FALSE OR ctype_digit($hour) == FALSE OR $hour >= 25)
		{
			return array('type' => 'general', 'msg' => $this->EE->lang->line('form:inv_time_format'));
		}

		if ($minute == FALSE OR ctype_digit($minute) == FALSE OR $minute >= 60)
		{
			return array('type' => 'general', 'msg' => $this->EE->lang->line('form:inv_time_format'));
		}



		if (isset($field['settings']['time_format']) == TRUE && $field['settings']['time_format'] == '12h')
		{
			if ($hour >= 13) return array('type' => 'general', 'msg' => $this->EE->lang->line('form:inv_time_format'));
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		return (is_array($data) == TRUE) ? serialize($data) : $data;
	}

	// ********************************************************************************* //

	public function output_data($field=array(), $data, $type='html')
	{
		$data = @unserialize($data);

		if (isset($data['hour']) == FALSE OR isset($data['minute']) == FALSE) return '';

		$time_format = '12h';

		if (isset($field['settings']['time_format']) == TRUE && $field['settings']['time_format'] == '24h')
		{
			$time_format = '24h';
		}

		// It can occur that we don't have it!
		if (isset($data['ampm']) == FALSE) $data['ampm'] = 'AM';

		switch ($time_format)
		{
			case '24h':
				$time = strtotime("2012/01/01 {$data['hour']}:{$data['minute']}");
				$out = date('H:i', $time);
				break;
			default:
				$time = strtotime("2012/01/01 {$data['hour']}:{$data['minute']} {$data['ampm']}");
				$out = date('h:i A', $time);
				break;
		}

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		return $this->EE->load->view('fields/time', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.time.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.time.php */
