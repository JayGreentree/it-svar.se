<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2013, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Logging Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */

if (class_exists('EE_Localize') === false) require_once(EE_APPPATH.'libraries/Localize'.EXT);

if (class_exists('Installer_Localize') === false) {


    class Installer_Localize extends EE_Localize {
    	// Nothing to see here.
    }
    // END CLASS
    //
}

/* End of file Functions.php */
/* Location: ./system/expressionengine/installer/libraries/Functions.php */
