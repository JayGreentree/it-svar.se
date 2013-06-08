<?php if ( ! defined('EXT')) exit('No direct script access allowed');

/**
 * Selected - Config
 *
 * NSM Addon Updater config file.
 *
 * @package		Solspace:Selected
 * @author		Solspace, Inc.
 * @copyright	Copyright (c) 2007-2013, Solspace, Inc.
 * @link		http://solspace.com/docs/selected
 * @license		http://www.solspace.com/license_agreement
 * @version		2.1.0
 * @filesource	selected/config.php
 */

//since we are 1.x/2.x compatible, we only want this to run in 1.x just in case
if (APP_VER >= 2.0)
{
	$config['name']    								= 'Selected';
	$config['version'] 								= '2.1.0';
	$config['nsm_addon_updater']['versions_xml'] 	= 'http://www.solspace.com/software/nsm_addon_updater/selected';
}