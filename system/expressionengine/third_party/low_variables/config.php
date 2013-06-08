<?php

/**
 * Low Variables config file
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

if ( ! defined('LOW_VAR_NAME'))
{
	define('LOW_VAR_NAME',         'Low Variables');
	define('LOW_VAR_PACKAGE',      'low_variables');
	define('LOW_VAR_VERSION',      '2.3.5');
	define('LOW_VAR_DEFAULT_TYPE', 'low_textarea');
	define('LOW_VAR_DOCS',         'http://gotolow.com/addons/low-variables');
}


/**
 * < EE 2.6.0 backward compat
 */
if ( ! function_exists('ee'))
{
	function ee()
	{
		static $EE;
		if ( ! $EE) $EE = get_instance();
		return $EE;
	}
}

/**
 * NSM Addon Updater
 */
$config['name']    = LOW_VAR_NAME;
$config['version'] = LOW_VAR_VERSION;
$config['nsm_addon_updater']['versions_xml'] = LOW_VAR_DOCS.'/feed';
