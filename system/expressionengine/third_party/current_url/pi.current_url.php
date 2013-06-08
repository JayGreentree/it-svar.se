<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Current URL',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Beardsell',
	'pi_author_url' => 'http://www.carterdigital.com.au',
	'pi_description' => 'Get the current URL',
	'pi_usage' => Current_url::usage()
);

class Current_url
{
	public $return_data = '';
	
	public function __construct()
	{
		$this->EE = get_instance();
		$this->EE->load->helper('url');
	}
	
	public function full_url()
	{
		return current_url();
	}
	

	
	public static function usage()
	{
		ob_start(); 
?>
{exp:current_url:full_url}
<?php
		$buffer = ob_get_contents();
		      
		ob_end_clean(); 
	      
		return $buffer;
	}
}
/* End of file pi.current_url.php */ 
/* Location: ./system/expressionengine/third_party/json/pi.current_url.php */