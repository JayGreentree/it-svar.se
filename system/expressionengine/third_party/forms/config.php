<?php

/**
 * Config file for Forms
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://ee-garage.com/nsm-addon-updater/developers
 */

if ( ! defined('FORMS_NAME'))
{
	define('FORMS_NAME',         'Forms');
	define('FORMS_CLASS_NAME',   'forms');
	define('FORMS_VERSION',      '3.3.5');
}

$config['name'] 	= FORMS_NAME;
$config["version"] 	= FORMS_VERSION;
$config['nsm_addon_updater']['versions_xml'] = 'http://www.devdemon.com/'.FORMS_CLASS_NAME.'/versions_feed/';

/* End of file config.php */
/* Location: ./system/expressionengine/third_party/forms/config.php */
