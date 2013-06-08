<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms FIELDSET field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_fieldset extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Fieldset',
		'name' 		=>	'fieldset',
		'category'	=>	'page_tools',
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

		$this->show_field_label = FALSE;
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		// Fieldset Legend
		if (isset($field['settings']['legend']) == TRUE) $legend = $field['settings']['legend'];
		else $legend = 'Untitled';

		$out = '<fieldset>';
		$out .= '<legend>'.$legend.'</legend>';
		$out .= '<div class="dfcolumns">';

		$out .= '<div class="column column-last sortable noborder" data-number="1" style="width:99%;">';
		if (isset($field['columns'][1]) === TRUE)
		{
			foreach ($field['columns'][1] as $html)
			{
				$out .= $html;
			}

		}
		$out .= '</div>';

		$out .= '<br clear="all"></div>';
		$out .= '</fieldset>';

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		return $this->EE->load->view('fields/fieldset', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.fieldset.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.fieldset.php */
