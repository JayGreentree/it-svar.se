<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms COLUMNS 2 field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_columns_2 extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'2 Columns',
		'name' 		=>	'columns_2',
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
		// column_width_1 Legend
		if (isset($field['settings']['column_width_1']) == TRUE) $column_width_1 = $field['settings']['column_width_1'];
		else $column_width_1 = '50%';

		// column_width_2 Legend
		if (isset($field['settings']['column_width_2']) == TRUE) $column_width_2 = $field['settings']['column_width_2'];
		else $column_width_2 = '50%';

		$out = '<div class="dfcolumns">';

		$out .= '<div class="column sortable" data-number="1" style="width:'.$column_width_1.'">';
		if (isset($field['columns'][1]) === TRUE)
		{
			foreach ($field['columns'][1] as $html)
			{
				$out .= $html;
			}

		}
		$out .= '</div>';

		$out .= '<div class="column column-last sortable" data-number="2" style="width:'.$column_width_2.'">';
		if (isset($field['columns'][2]) === TRUE)
		{
			foreach ($field['columns'][2] as $html)
			{
				$out .= $html;
			}
		}
		$out .= '</div>';

		$out .= '<br clear="all"></div>';

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		unset($GLOBALS['column_width_1'], $GLOBALS['column_width_2'], $GLOBALS['column_width_3'], $GLOBALS['column_width_4']);
		$vData = $settings;

		return $this->EE->load->view('fields/columns_2', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.columns_2.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.columns_2.php */
