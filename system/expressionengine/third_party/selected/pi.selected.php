<?php

/**
 * Selected - Plugin
 *
 * @package		Solspace:Selected
 * @author		Solspace, Inc.
 * @copyright	Copyright (c) 2007-2013, Solspace, Inc.
 * @link		http://solspace.com/docs/selected
 * @license		http://www.solspace.com/license_agreement
 * @version		2.1.0
 * @filesource	selected/pi.selected.php
 */

$plugin_info = array(
	'pi_name'			=> 'Selected',
	'pi_version'		=> '2.1.0', //change this in the config.php as well
	'pi_author'			=> 'Solspace, Inc.',
	'pi_author_url'		=> 'http://www.solspace.com/',
	'pi_description'	=> 'Dynamically parse CSS objects or select states of form elements at page load time.',
	'pi_usage'			=> Selected::usage()
);


class Selected {

    var $return_data;	
    
    // ----------------------------------------
    //  Selected
    // ----------------------------------------
    //	This function takes a bit of input and
    //	returns a selected state if conditions
    //	are met.
    // ----------------------------------------

    function selected ()
    {
    	if ( APP_VER < '2.0' )
    	{
			global $TMPL;
    	}
    	else
    	{
			$this->EE 	=& get_instance();			
			$TMPL 		=& $this->EE->TMPL;
    	}    	

		$tagdata	= $TMPL->tagdata;

		$item		= ( $TMPL->fetch_param('item') ) 	? $TMPL->fetch_param('item')	: '';
		$replace	= ( $TMPL->fetch_param('replace') ) ? $TMPL->fetch_param('replace')	: ' class="selected"';
		
		if ( preg_match( "/".LD."selected_".$item.RD."/s", $tagdata, $match ) )
		{
			$tagdata	= str_replace( $match['0'], $replace, $tagdata );
		}
		elseif ( $item == '' )
		{
			$tagdata	= str_replace( LD.'selected_empty'.RD, $replace, $tagdata );
		}
		else
		{
			$tagdata	= str_replace( LD.'selected_null'.RD, $replace, $tagdata );
		}
		
		$tagdata	= preg_replace( "/".LD."selected_.*?".RD."/s", "", $tagdata );
		
		$this->return_data	= $tagdata;
    }
    
    //	End selected
	
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
// Make sure and use output buffering

function usage()
{
ob_start(); 
?>
The purpose of this plugin is to make it easier to dynamically parse CSS objects or select states of form elements at page load time. You might typically use this for applying a 'selected' class to a navigation menu.

The Selected plugin checks the segment you've told it to look at and compares it against your code. When the segment matches, Selected will apply the code you specified to your template (usually something like class="selected").

Full Documentation can be found here:
http://www.solspace.com/docs/selected/
<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END


}
// END CLASS
?>