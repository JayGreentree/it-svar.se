<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormsUpdate_323
{

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		// Load dbforge
		$this->EE->load->dbforge();
	}

	// ********************************************************************************* //

	public function do_update()
	{
		// We need to do it manually since EE's installer is heavily dependent on the CP Class
		require_once PATH_THIRD.'forms/ext.forms.php';
		$class = 'Forms_ext';
		$EXT = new $class();

		if (method_exists($EXT, 'activate_extension') === TRUE)
		{
			$activate = $EXT->activate_extension();
		}
	}

	// ********************************************************************************* //

}

/* End of file 321.php */
/* Location: ./system/expressionengine/third_party/forms/updates/321.php */
