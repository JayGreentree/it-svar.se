<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
if (file_exists(PATH_THIRD.'forms/config.php') === TRUE) include PATH_THIRD.'forms/config.php';
else include dirname(dirname(__FILE__)).'/forms/config.php';

/**
 * Channel Forms Module FieldType
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class Forms_linked_ft extends EE_Fieldtype
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> 'Forms Linked',
		'version'	=> FORMS_VERSION
	);

	/**
	 * The field settings array
	 *
	 * @access public
	 * @var array
	 */
	public $settings = array();

	public $has_array_data = TRUE;

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		if (version_compare(APP_VER, '2.1.4', '>')) { parent::__construct(); } else { parent::EE_Fieldtype(); }

		$this->EE->load->add_package_path(PATH_THIRD . 'forms/');
		$this->EE->lang->loadfile('forms');
		$this->EE->load->library('forms_helper');
		$this->EE->load->model('forms_model');
		$this->EE->forms_helper->define_theme_url();
		$this->EE->config->load('forms_config');

		$this->site_id = $this->EE->forms_helper->get_current_site_id();
	}

	// ********************************************************************************* //

	function display_field($data)
	{
		$data = trim($data);
		$options = array('');

		$query = $this->EE->db->select('form_title, form_id')->from('exp_forms')->where('entry_id', 0)->order_by('form_title', 'ASC')->get();

		foreach ($query->result() as $row)
		{
			$options[ $row->form_id ] = $row->form_title . '&nbsp;&nbsp;&nbsp;';
		}

		return form_dropdown($this->field_name, $options, $data, ' style="padding:3px; border:1px solid #ccc;" ');
	}

	// ********************************************************************************* //

	public function replace_tag($data, $params=array(), $tagdata=FALSE)
	{
		$data = trim($data);
		if ($data == FALSE) return;

		if (class_exists('Forms') == FALSE) include PATH_THIRD.'forms/mod.forms.php';
		$F = new Forms();

		// Lets cache the entire entry row
		$this->EE->session->cache['forms']['ee_entry_row'] = $this->row;

		$params['form_id'] = $data;
		return $F->form($params, $tagdata);
	}

	// ********************************************************************************* //



}

/* End of file ft.forms.php */
/* Location: ./system/expressionengine/third_party/forms/ft.forms.php */
