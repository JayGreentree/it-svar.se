<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Channel Forms Field File
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 */
class FormsField
{
	/**
	 * Render this field?
	 *
	 * @var bool
	 * @access protected
	 */
	protected $show_field = TRUE;

	/**
	 * Show Element Wrappers?
	 *
	 * @var bool
	 * @access protected
	 */
	protected $show_wrappers = TRUE;

	/**
	 * Show Field Labels?
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $show_field_label = TRUE;

	/**
	 * Convert this field to a hidden input?
	 *
	 * @var bool
	 * @access protected
	 */
	protected $hidden_field = FALSE;

	/**
	 * Hidden input value
	 *
	 * @var string
	 * @access protected
	 */
	protected $hidden_field_value = '';

	/**
	 * Return directly
	 *
	 * @var string
	 * @access protected
	 */
	protected $return_directly = FALSE;

	public $default_settings = array();


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		// Creat EE Instance
		$this->EE =& get_instance();
		$this->site_id = $this->EE->config->item('site_id');
	}

	// ********************************************************************************* //

	/**
	 * Render the field!
	 *
	 * @param array $field - The Field Array
	 * @param bool $template - Rendering on the template? or CP
	 * @param mixed $data - The field data array from _POST
	 */
	public function render_field($field=array(), $template=TRUE, $data='')
	{
		return '';
	}

	// ********************************************************************************* //

	/**
	 * Display Field
	 * Creates the HTML wrapeprs and labels etc. Then calls render_field().
	 *
	 * @param array $field - The Field Array
	 * @param bool $template - Rendering on the template? or CP
	 */
	public function display_field($field=array(), $template=TRUE)
	{
		// Reset Everything again
		$this->show_field_label = TRUE;

		// Some Vars
		$display = '';
		$css_js = '';

		//----------------------------------------
		// Parse Conditionals
		//----------------------------------------
		if (isset($field['conditionals']) == TRUE && $field['conditionals'] != FALSE)
		{
			$field['conditionals'] = @unserialize($field['conditionals']);
			if (is_array($field['conditionals']) == FALSE) $field['conditionals'] = array();
		}
		else $field['conditionals'] = array();

		// Conditional Enabled?
		if (isset($field['conditionals']['options']['enable']) === TRUE && $field['conditionals']['options']['enable'] == 'yes')
		{
			$display .= ( $this->process_field_conditionals($field) ) ? 'display:block' : 'display:none';
			$css_js .= $this->EE->forms_helper->output_js_buffer('<script type="text/javascript">if (typeof(window.Forms) != \'undefined\') Forms.AddConditional('.$field['field_id'].','.$this->EE->forms_helper->generate_json($field['conditionals']).');</script>');
		}

		//----------------------------------------
		// Label/Desc Placements
		//----------------------------------------
		$label_place = (isset($field['form_settings']['label_placement']) == TRUE) ? $field['form_settings']['label_placement'] : 'top';
		$desc_place = (isset($field['form_settings']['desc_placement']) == TRUE) ? $field['form_settings']['desc_placement'] : 'bottom';

		// Disable Label?
		if (isset($field['form_settings']['label_placement']) == TRUE && $field['form_settings']['label_placement'] == 'none')
		{
			$this->show_field_label = FALSE;
		}

		// Now we have some exceptions ofcourse!
		$exceptions = array('columns_2', 'columns_3', 'columns_4', 'fieldset');
		if (in_array($field['field_type'], $exceptions) === TRUE)
		{
			$this->show_field_label = FALSE;
			$label_place = 'top';
		}

		// Override Label
		if (isset($field['show_label']) === TRUE)
		{
			if ($field['show_label'] == 2) $this->show_field_label = TRUE;
			if ($field['show_label'] == 0) $this->show_field_label = FALSE;
		}

		// Override Label Placement
		if (isset($field['label_position']) === TRUE && $field['label_position'] != FALSE && $field['label_position'] != 'auto')
		{
			$label_place = $field['label_position'];
		}

		// Lets add field_name just in case (form input name="")
		if (isset($field['form_name']) == FALSE) $field['form_name'] = '';
		if (isset($field['form_elem_id']) == FALSE) $field['form_elem_id'] = '';

		//----------------------------------------
		// Rendering in the PBF/MCP?
		//----------------------------------------
		if ($template == FALSE)
		{
			// Empty Form Name Always!
			$field['form_name'] = '';
			$field['form_elem_id'] = '';

			// Is it a pagebreak!
			if ($this->info['name'] == 'pagebreak')
			{
				return $this->render_field($field['settings'], $template);
			}
		}

		//----------------------------------------
		// Add CSS Classes
		//----------------------------------------
		$main_class = array($this->info['name']);
		if ($label_place == 'left_align')	$main_class[] = 'dfleft_label';
		if ($label_place == 'right_align')	$main_class[] = 'dfright_label';
		if ($label_place == 'top')		$main_class[] = 'dftop_label';
		if ($label_place == 'bottom')	$main_class[] = 'dfbottom_label';

		// We need a field_id
		if (isset($field['field_id']) == FALSE) $field['field_id'] = 0;

		// Required?
		$required_class = (isset($field['required']) == TRUE && $field['required'] == 1) ? 'dform_required' : '';
		$required_span = (isset($field['required']) == TRUE && $field['required'] == 1) ? ' <span class="req">*</span>' : '';

		//----------------------------------------
		// Render!
		//----------------------------------------
		$out = '';
		$out .= '<div class="dform_element dform_'.implode(' ', $main_class).' '.$required_class.'" id="forms_field_'.$field['field_id'].'" style="'.$display.'">';

		if ($this->show_field_label == TRUE)
		{
			// Label Placement Top?
			if ($label_place != 'bottom') $out .= '<label class="dform_label" for="'.$field['form_elem_id'].'">' . $field['title'] .$required_span. '</label>';
		}

		// Desc Placement Top?
		if ($desc_place == 'top' && $field['description'] != FALSE) $out .= '<p class="dform_desc">' . $field['description'] . '</p>';



		// Are they any erros?
		if (isset($_POST['forms_errors'][$field['field_id']]['msg']) == TRUE)
		{
			$out .= '<div class="dform_error">' . $_POST['forms_errors'][ $field['field_id'] ]['msg'] . '</div>';
		}

		// We need to add formdata back..
		$data = array();
		if (isset($_POST['fields'][ $field['field_id'] ])) $data = $_POST['fields'][ $field['field_id'] ];

		$field_body = $this->render_field($field, $template, $data);

		if ($this->return_directly == FALSE)
		{
			// Render the field
			$out .=	'<div class="dform_container">' . $field_body . '</div>';
		}
		else
		{
			return $field_body;
		}


		if ($this->show_field_label == TRUE)
		{
			// Label Placement BOTTOM?
			if ($label_place == 'bottom') $out .= '<label class="dform_label" for="'.$field['form_elem_id'].'">' . $field['title'] .$required_span. '</label>';
		}

		// Do we need to add a break
		if ($label_place == 'left_align' || $label_place == 'right_align') $out .= '<br clear="all">';

		// Desc Placement BOTTOM?
		if ($desc_place == 'bottom' && $field['description'] != FALSE) $out .= '<div class="dform_desc">' . $field['description'] . '</div>';

		$out .= $css_js;
		$out .= '</div>';


		//----------------------------------------
		// Hidden field override!
		//----------------------------------------
		if ($this->hidden_field === TRUE && $template === TRUE)
		{
			// Can we find: format=""?
			if (strpos($this->hidden_field_value, 'format=') !== FALSE)
			{
				// Reason? form_hidden() converts double quotes to html entities!
				$out = "<div class='hiddenFields'><input type='hidden' name='{$field['form_name']}' value='{$this->hidden_field_value}'></div>";
			}
			else
			{
				$out = '<div class="hiddenFields">'.form_hidden($field['form_name'], $this->hidden_field_value).'</div>';
			}
		}

		//----------------------------------------
		// Last chance to override show_field
		//----------------------------------------
		if ($this->show_field == FALSE)
		{
			return '';
		}

		return $out;
	}

	// ********************************************************************************* //

	/**
	 * Validate user input
	 *
	 * @param array $field - The Field Array
	 * @param mixed $data - The submitted data. Can be string or array
	 * @access public
	 * @return mixed - Return TRUE on success or array/string on failure
	 */
	public function validate($field=array(), $data)
	{
		return TRUE;
	}

	// ********************************************************************************* //

	public function precheck_save($field=array(), $data)
	{
		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Prepare to save the field
	 *
	 * @param array $field - The field array
	 * @param mixed $data - The field data to be saved
	 * @access public
	 * @return string - ALWAYS
	 */
	public function save($field=array(), $data)
	{
		return (string) $data;
	}

	// ********************************************************************************* //

	/**
	 * Prepare Output Data
	 *
	 * @param array $field - The field
	 * @param string $data - The raw data from the DB
	 * @param string $type - line/text/html
	 * @access public
	 * @return string - The data to be outputted
	 */
	public function output_data($field=array(), $data, $type='html')
	{
		return (string) $data;
	}

	// ********************************************************************************* //

	public function display_settings($field=array(), $template=TRUE)
	{
		// We need settings
		if (isset($field['settings']) == FALSE) $field['settings'] = array();

		// Final Output
		$out = '';

		// Add some global vars!
		$vars = array();
		$vars['form_name_settings'] = isset($field['form_name_settings']) ? $field['form_name_settings'] : 'settings';

		$this->EE->load->vars($vars);

		// Execute the settings method
		$out = $this->field_settings($field['settings'], $template=TRUE);

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		return '';
	}

	// ********************************************************************************* //

	public function save_settings($settings=array())
	{
		return $settings;
	}

	// ********************************************************************************* //

	public function delete_field($field)
	{

	}

	// ********************************************************************************* //

	protected function get_cart_total()
	{
		$total = 0;

		if (isset($this->EE->forms->cart['products']) == false)
		{
			$this->EE->forms->cart['products'] = array();
		}

		foreach ($this->EE->forms->cart['products'] as &$product)
		{
			$product['options_total'] = 0;
			$product['product_total'] = 0;
			$product['line_total'] = 0;

			if (isset($product['price']) === FALSE) continue;

			// Any Product Options?
			if (isset($product['option']) === TRUE)
			{
				foreach ($product['option'] as $option)
				{
					if (isset($option['price']) === FALSE) continue;
					$product['options_total'] += $option['price'];
				}
			}

			$product['product_total'] = $product['price'] + $product['options_total'];

			if (isset($product['qty']) === TRUE) $product['line_total'] += ($product['qty'] * $product['product_total']);
			else $product['line_total'] += $product['product_total'];

			$total += $product['line_total'];
		}

		// Shipping?
		if (isset($this->EE->forms->cart['shipping']['price']) === TRUE)
		{
			$total += $this->EE->forms->cart['shipping']['price'];
		}

		return number_format($total, 2, '.', '');
	}

	// ********************************************************************************* //

	public function process_field_conditionals($field)
	{
		// Just to be sure lets double check
		if (isset($field['conditionals']['options']['enable']) === FALSE || $field['conditionals']['options']['enable'] == 'no')
		{
			return TRUE;
		}

		$display = TRUE;

		if ($field['conditionals']['options']['display'] == 'show') $display = FALSE;

		foreach ($field['conditionals']['conditionals'] as &$cond)
		{
			if (isset($_POST['fields'][ $cond['field'] ]) === TRUE && $_POST['fields'][ $cond['field'] ] != FALSE)
			{
				$field_val = $_POST['fields'][ $cond['field'] ];

				// Is it an array? a product field for example?
				if (is_array($field_val) === true) {
					if (isset($field_val['product']) === true) {
						$field_val = $field_val['product'];
					}
				}

				$val = strtolower(trim($field_val));
				$cond['value'] = trim(strtolower($cond['value']));

				/*
				if ($field['field_id'] == 2) {
					$this->EE->firephp->log($val);
					$this->EE->firephp->log($cond);
				}
				*/

				switch ($cond['operator'])
				{
					case 'is':
						if ($val == $cond['value']) $cond['passed'] = TRUE;
						break;
					case 'isnot':
						if ($val != $cond['value']) $cond['passed'] = TRUE;
						break;
					case 'greater_then':
						if ( (int) $val > (int) $cond['value']) $cond['passed'] = TRUE;
						break;
					case 'less_then':
						if ( (int) $val < (int) $cond['value']) $cond['passed'] = TRUE;
						break;
					case 'contains':
						if ( strpos($cond['value'], $val) !== FALSE ) $cond['passed'] = TRUE;
						break;
					case 'starts_with':
						if ( strpos($cond['value'], $val) === 0 ) $cond['passed'] = TRUE;
						break;
					case 'ends_with':
						if ( substr($val, -strlen($cond['value'])) == $val ) $cond['passed'] = TRUE;
						break;
				}
			}
			else
			{
				$cond['passed'] = FALSE;
			}

			//if ($field['field_id'] == 2) $this->EE->firephp->log($cond);
		}

		if ($field['conditionals']['options']['match'] == 'all')
		{
			$passed_all = TRUE;

			foreach ($field['conditionals']['conditionals'] as &$cond)
			{
				if (isset($cond['passed']) === FALSE OR $cond['passed'] == FALSE) $passed_all = FALSE;
			}

			if ($passed_all == TRUE)
			{
				$display = TRUE;
			}
		}
		else
		{
			$passed_any = FALSE;

			foreach ($field['conditionals']['conditionals'] as &$cond)
			{
				if (isset($cond['passed']) === TRUE && $cond['passed'] == TRUE) $passed_any = TRUE;
			}

			if ($passed_any == TRUE)
			{
				$display = TRUE;
			}
		}

		return $display;
	}

	// ********************************************************************************* //


} // END CLASS

/* End of file cf_field.php  */
/* Location: ./system/expressionengine/third_party/channel_fields/fields/cf_field.php */
