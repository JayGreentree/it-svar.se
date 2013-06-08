<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Fieldtype Bridge Class
 *
 * Acts as bridge between variable types and fieldtypes
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

class Low_fieldtype_bridge {

	/**
	 * Default settings fallback
	 *
	 * @var array
	 */
	public $default_settings = array();

	// --------------------------------------------------------------------

	/**
	 * PHP4 Constructor
	 *
	 * @see	__construct()
	 */
	public function Low_fieldtype_bridge($info = array())
	{
		$this->__construct($info);
	}

	// --------------------------------------------------------------------

	/**
	 * PHP5 Constructor
	 *
	 * @param	array	$info
	 * @return	void
	 */
	public function __construct($info = array())
	{
		if ($info)
		{
			$this->info = $info;
			$this->ftype = new $info['class'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 *
	 * @param	mixed	$var_id			The id of the variable: 'new' or numeric
	 * @param	array	$var_settings	The settings of the variable
	 * @return	array
	 */
	public function display_settings($var_id, $var_settings)
	{
		if (method_exists($this->ftype, 'display_var_settings'))
		{
			$this->ftype->var_id = $var_id;
			$var_settings = $this->ftype->display_var_settings($var_settings);
		}

		return (array) $var_settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Do something with settings before saving them to the DB
	 *
	 * @param	mixed	$var_id			The id of the variable: 'new' or numeric
	 * @param	array	$var_settings	The settings of the variable
	 * @return	array
	 */
	public function save_settings($var_id, $var_settings)
	{
		if (method_exists($this->ftype, 'save_var_settings'))
		{
			if ($var_id != 'new') $this->ftype->var_id = $var_id;
			$var_settings = $this->ftype->save_var_settings($var_settings);
		}

		return $var_settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Do something after the variable has been saved
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	array	$var_settings	The settings of the variable
	 * @return	void
	 */
	public function post_save_settings($var_id, $var_settings)
	{
		if (method_exists($this->ftype, 'post_save_var_settings'))
		{
			$this->ftype->var_id = $var_id;
			$this->ftype->post_save_var_settings($var_settings);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display input field for regular user
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	string	$var_data		The value of the variable
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	public function display_input($var_id, $var_data, $var_settings)
	{
		$var_settings['field_name'] = $this->ftype->field_name = "var[{$var_id}]";

		$this->ftype->var_id = $var_id;
		$this->ftype->settings = $var_settings;

		return $this->ftype->display_var_field($var_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep variable data for saving
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	mixed	$var_data		The value of the variable, array or string
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	public function save_input($var_id, $var_data, $var_settings)
	{
		if (method_exists($this->ftype, 'save_var_field'))
		{
			$var_settings['field_name'] = $this->ftype->field_name = "var[{$var_id}]";

			$this->ftype->var_id = $var_id;
			$this->ftype->settings = $var_settings;

			$var_data = $this->ftype->save_var_field($var_data);
		}

		return $var_data;
	}

	// --------------------------------------------------------------------

	/**
	 * Do something after the variable has been saved to the DB
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	mixed	$var_data		The value of the variable, array or string
	 * @param	array	$var_settings	The settings of the variable
	 * @return	mixed
	 */
	public function post_save_input($var_id, $var_data, $var_settings)
	{
		if (method_exists($this->ftype, 'post_save_var'))
		{
			$this->ftype->var_id = $var_id;
			$this->ftype->settings = $var_settings;

			return $this->ftype->post_save_var($var_data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display template tag output
	 *
	 * @param	string	$tagdata	Tagdata of template tag
	 * @param	array	$data		Data of the variable, containing id, data, settings...
	 * @return	mixed				String if successful, FALSE if not
	 */
	public function display_output($tagdata, $data)
	{
		if (method_exists($this->ftype, 'display_var_tag'))
		{
			$this->ftype->var_id   = $data['variable_id'];
			$this->ftype->settings = $data['variable_settings'];

			return $this->ftype->display_var_tag($data['variable_data'], ee()->TMPL->tagparams, $tagdata);
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Do stuff after a variable has been deleted
	 *
	 * @param	int		$var_id		Variable id that's been deleted
	 * @return	void
	 */
	public function delete($var_id)
	{
		if (method_exists($this->ftype, 'delete_var'))
		{
			$this->ftype->var_id = $var_id;
			return $this->ftype->delete_var($var_id);
		}
	}

	// --------------------------------------------------------------------

	public function load_assets()
	{
		return FALSE;
	}
}