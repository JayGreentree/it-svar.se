<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Responsive CP Accessory
 *
 * @package		Responsive CP
 * @category	Accessory
 * @author		Ben Croker
 * @link		http://www.putyourlightson.net/responsive-cp/
 */
 

class Responsive_cp_acc
{
	var $name	 		= 'Responsive CP';
	var $id	 			= 'responsive_cp';
	var $version	 	= '1.2';
	var $description	= 'Makes CP theme responsive';
	var $sections	 	= array();
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();

		// backwards compabitility for EE < 2.4
		if (!defined('URL_THIRD_THEMES'))
		{
			define('URL_THIRD_THEMES', $this->EE->config->item('theme_folder_url').'third_party/');
			define('PATH_THIRD_THEMES', $this->EE->config->item('theme_folder_path').'third_party/');
		}
	} 

	// --------------------------------------------------------------------
	
	/**
	* Set Sections
	*/
	function set_sections()
	{
		// hide accessory
		$this->sections[] = '<script type="text/javascript">$("#accessoryTabs a.responsive_cp").parent().remove();</script>';

		// get theme folder
		$theme_folder_url = URL_THIRD_THEMES.'responsive_cp/';

		// add meta tag
		$this->EE->cp->add_to_head('<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />');
		
		// link css file
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$theme_folder_url.'css/responsive_cp.css" media="screen" />');
				
		// insert all images in logos folder
		$logo_path = PATH_THIRD_THEMES.'responsive_cp/logos/';		
		
		if ($files = @scandir($logo_path))
		{
			$images = array();
			
			foreach ($files as $file)
			{
				// if file exists and is an image
				if (is_file($logo_path.$file) AND getimagesize($logo_path.$file))
				{
					$images[] = '<img src="'.$theme_folder_url.'logos/'.$file.'" />';					
				}
			}
			
			if (count($images))
			{
				$js = '$("#footer").prepend(\''.implode(' ', $images).'<br/><br/>\');';
				$this->EE->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');		
			}
		}
	}

}
// END CLASS

/* End of file acc.responsive_cp.php */
/* Location: ./system/expressionengine/third_party/responsive_cp/acc.responsive_cp.php */