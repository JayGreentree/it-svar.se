<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms MULTISELECT field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_multiselect extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Multi Select',
		'name' 		=>	'multiselect',
		'category'	=>	'form_tools',
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

		$this->default_settings['choices'][]  = array('label' => 'First', 'value' => 'First');
		$this->default_settings['choices'][]  = array('label' => 'Second', 'value' => 'Second');
		$this->default_settings['choices'][]  = array('label' => 'Third', 'value' => 'Third');
		$this->default_settings['values_enabled'] = 'no';
		$this->default_settings['default_choice'] = -1;
		$this->default_settings['enhanced_ui'] = 'yes';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$out  = '';

		$field['settings'] = array_merge($this->default_settings, $field['settings']);

		// Use Values?
		$use_values = ($field['settings']['values_enabled'] == 'yes') ? TRUE : FALSE;

		// Default Choice (always INT)
		if (isset($field['settings']['default_choice']) == FALSE) $field['settings']['default_choice'] = '';
		else $field['settings']['default_choice'] = (int) $field['settings']['default_choice'];

		// -----------------------------------------
		// Do we have any previous submits!
		// -----------------------------------------
		$check_submit = FALSE;
		if (is_array($data) == TRUE && empty($data) == FALSE)
		{
			$check_submit = TRUE;
		}

		// -----------------------------------------
		// Loop over all items!
		// -----------------------------------------
		$items = array();
		foreach ($field['settings']['choices'] as $number => $choice)
		{
			// What Value are we going to use?
			$value = ($use_values ? $choice['value'] : $choice['label']);
			$items[$value] = $choice['label'];

			if ($check_submit == FALSE && $field['settings']['default_choice'] === $number) $data = array($value);
		}

		// Select CLass
		$class = '';
		if (isset($field['settings']['enhanced_ui']) == TRUE && $field['settings']['enhanced_ui'] == 'yes')
		{
			$class .= ' chzn-select ';
		}

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$class .= ' required validate[required] ';
			}
		}

		// -----------------------------------------
		// Render
		// -----------------------------------------
		$out =	form_multiselect($field['form_name'].'[]', $items, $data, ' class="'.$class.'" id="'.$field['form_elem_id'].'" ');

		// -----------------------------------------
		// Add Chosen!
		// -----------------------------------------
		if (isset($field['settings']['enhanced_ui']) == TRUE && $field['settings']['enhanced_ui'] == 'yes')
		{
			if ( isset($this->EE->session->cache['DevDemon']['JS']['jquery.chosen']) == FALSE )
			{
				$url = $this->EE->forms_helper->define_theme_url();

				$out .= $this->EE->forms_helper->output_js_buffer('<script src="' . $url . 'chosen/jquery.chosen.js" type="text/javascript"></script>');
				$out .= '<link rel="stylesheet" href="' . FORMS_THEME_URL . 'chosen/chosen.css" type="text/css" media="print, projection, screen" />';

			}

			if ($template == FALSE) $out .= '<script type="text/javascript">jQuery("select.chzn-select").css({width:"80%"}).chosen();</script>';
			else $out .= $this->EE->forms_helper->output_js_buffer('<script type="text/javascript">jQuery("select.chzn-select").chosen();</script>');
		}

		return $out;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		return serialize($data);
	}

	// ********************************************************************************* //

	public function output_data($field=array(), $data, $type='template')
	{
		$data = unserialize($data);

		if ($data == FALSE OR is_array($data) == FALSE) return '';

		if (isset($this->EE->TMPL->tagparams['format_multiple']) == TRUE)
		{
			$out = '';
			foreach ($data as $val)
			{
				if ($val == FALSE) continue;
				$out .= str_replace('%value%', $val, $this->EE->TMPL->tagparams['format_multiple']);
			}
			return $out;
		}

		return implode(', ', $data);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$settings = array_merge($this->default_settings, $settings);
		$vData = $settings;

		if (isset($vData['choices']) == FALSE OR is_array($vData['choices']) == FALSE)
		{
			$vData['choices'][]  = array('label' => 'First', 'value' => 'First');
			$vData['choices'][]  = array('label' => 'Second', 'value' => 'Second');
			$vData['choices'][]  = array('label' => 'Third', 'value' => 'Third');
		}

		// Default Choice (always INT)
		if (isset($vData['default_choice']) == FALSE) $vData['default_choice'] = '';
		else $vData['default_choice'] = (int) $vData['default_choice'];

		// Default Choice
		if (isset($vData['enhanced_ui']) == FALSE) $vData['enhanced_ui'] = 'yes';

		return $this->EE->load->view('fields/multiselect', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function save_settings($settings=array())
	{
		$settings = array_merge($this->default_settings, $settings);

		foreach($settings['choices'] as $number => &$choice)
		{
			$choice['label'] = trim($choice['label']);
			$choice['value'] = trim($choice['value']);

			if ($choice['label'] == FALSE) unset ($settings['choices'][$number]);
			if ($choice['value'] == FALSE) $choice['value'] = $choice['label'];
		}

		return $settings;
	}

	// ********************************************************************************* //

}

/* End of file field.multiselect.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.multiselect.php */
