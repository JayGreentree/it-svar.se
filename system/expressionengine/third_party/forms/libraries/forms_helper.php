<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forms Helper File
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 */
class Forms_helper
{
	private $package_name = 'forms';
	static $ekey = '0ZHM9AUdG9iEFUxBwlfEKkdi2m28pClFco0dX3rPIWfOsnmXzlJNakJawvEHkZ4';

	public function __construct()
	{
		// Creat EE Instance
		$this->EE =& get_instance();
		$this->EE->load->library('firephp');

		$this->site_id = $this->get_current_site_id();
		$this->AJAX = NULL;

		if (isset($this->EE->forms) == FALSE) $this->EE->forms = new stdClass();
		$this->EE->forms->colfields = array('columns_2', 'columns_3', 'columns_4', 'fieldset', 'html', 'pagebreak', 'html');

		if (isset($this->EE->forms->config) === FALSE)
		{
			$this->EE->forms->config = $this->EE->config->item('forms');
			if (is_array($this->EE->forms->config) === FALSE) $this->EE->forms->config = array();
		}
	}

	// ********************************************************************************* //

	public function output_js_buffer($js)
	{
		if (isset($this->EE->forms->queue_js) === TRUE && $this->EE->forms->queue_js == TRUE)
		{
			if (isset($this->EE->forms->js_buffer) === FALSE) $this->EE->forms->js_buffer = array();
			$this->EE->forms->js_buffer[] = $js;
			return '';
		}

		return $js;
	}

	// ********************************************************************************* //

	function define_theme_url()
	{
		if (defined('URL_THIRD_THEMES') === TRUE)
		{
			$theme_url = URL_THIRD_THEMES;
		}
		else
		{
			$theme_url = $this->EE->config->item('theme_folder_url').'third_party/';
		}

		// Are we working on SSL?
		if (isset($_SERVER['HTTP_REFERER']) == TRUE AND strpos($_SERVER['HTTP_REFERER'], 'https://') !== FALSE)
		{
			$theme_url = str_replace('http://', 'https://', $theme_url);
		}
		elseif (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')
		{
			$theme_url = str_replace('http://', 'https://', $theme_url);
		}

		if (! defined('FORMS_THEME_URL')) define('FORMS_THEME_URL', $theme_url . 'forms/');

		return FORMS_THEME_URL;
	}

	// ********************************************************************************* //

	function get_router_url($type='url', $method='ACT_general_router')
	{
		// Do we have a cached version of our ACT_ID?
		if (isset($this->EE->session->cache['Forms']['Router_Url'][$method]['ACT_ID']) == FALSE)
		{
			$this->EE->db->select('action_id');
			$this->EE->db->where('class', 'Forms');
			$this->EE->db->where('method', $method);
			$query = $this->EE->db->get('actions');
			$ACT_ID = $query->row('action_id');
		}
		else $ACT_ID = $this->EE->session->cache['Forms']['Router_Url'][$method]['ACT_ID'];

		// RETURN: Full Action URL
		if ($type == 'url')
		{
			// Grab Site URL
			$url = $this->EE->functions->fetch_site_index(0, 0);

			/*
			// Check for INDEX
			$site_index = $this->EE->config->item('site_index');

			if ($site_index != FALSE)
			{
				// Check for index.php
				if (substr($url, -9, 9) != 'index.php')
				{
					$url .= 'index.php';
				}
			}
			*/

			// Check for last slash
			//if (substr($url, -1) != '/') $url .= '/';

			if (defined('MASKED_CP') == FALSE OR MASKED_CP == FALSE)
			{
				// Replace site url domain with current working domain
				$server_host = (isset($_SERVER['HTTP_HOST']) == TRUE && $_SERVER['HTTP_HOST'] != FALSE) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
				$url = preg_replace('#http\://(([\w][\w\-\.]*)\.)?([\w][\w\-]+)(\.([\w][\w\.]*))?\/#', "http://{$server_host}/", $url);
			}

			// Create new URL
			$ajax_url = $url.QUERY_MARKER.'ACT=' . $ACT_ID;

			if (isset($this->EE->session->cache['Forms']['Router_Url'][$method]['URL']) == TRUE) return $this->EE->session->cache['Forms']['Router_Url'][$method]['URL'];
			$this->EE->session->cache['Forms']['Router_Url'][$method]['URL'] = $ajax_url;
			return $this->EE->session->cache['Forms']['Router_Url'][$method]['URL'];
		}

		// RETURN: ACT_ID Only
		if ($type == 'act_id') return $ACT_ID;
	}

	// ********************************************************************************* //

	public function getUserIp()
	{
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED') as $key){
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					$ip = trim($ip); // just to be safe

					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						return $ip;
					}
				}
			}
		}

		return $this->EE->input->ip_address();
	}

	// ********************************************************************************* //

	/**
	 * Grab File Module Settings
	 * @return array
	 */
	function grab_settings($site_id=true)
	{
		$settings = array();

		if (isset($this->EE->session->cache['forms']['module_settings']) == TRUE)
		{
			$settings = $this->EE->session->cache['forms']['module_settings'];
		}
		else
		{
			$this->EE->db->select('settings');
			$this->EE->db->where('module_name', 'Forms');
			$query = $this->EE->db->get('exp_modules');
			if ($query->num_rows() > 0) $settings = unserialize($query->row('settings'));
		}

		$this->EE->session->cache['forms']['module_settings'] = $settings;

		if ($site_id)
		{
			$settings = isset($settings['site:'.$site_id]) ? $settings['site:'.$site_id] : array();
		}

		$conf = $this->EE->config->item('forms_defaults');
		$settings = $this->array_extend($conf, $settings);

		return $settings;
	}

	// ********************************************************************************* //

    /**
     * Get Module Settings
     *
     * @param int site_id
     * @return array
     * @access public
     */
    public function get_module_settings($site_id=1)
    {
        $settings = array();

        if (isset($this->EE->session->cache[$this->package_name]['module_settings']) === true) {
            $settings = $this->EE->session->cache[$this->package_name]['module_settings'];
        } else {
            $this->EE->db->select('settings');
            $this->EE->db->where('module_name', ucfirst($this->package_name));
            $query = $this->EE->db->get('exp_modules');

            if ($query->num_rows() > 0) {
                $settings = @unserialize($query->row('settings'));
            }

            if (!$settings) {
                $settings = array();
            }

            $this->EE->session->cache[$this->package_name]['module_settings'] = $settings;
        }

        if ($site_id) {
            $settings = isset($settings['site:'.$site_id]) ? $settings['site:'.$site_id] : array();
        }

        $conf = $this->EE->config->item($this->package_name.'_defaults');
        $override_conf = $this->EE->config->item($this->package_name);

        if (is_array($override_conf) === false) {
            $override_conf = array();
        }

        $settings = $this->array_extend($conf, $settings);

        if (empty($override_conf) === false) {
            $settings = $this->array_extend($settings, $override_conf);
        }

        return $settings;
    }

    // ********************************************************************************* //

	public function is_ajax()
	{
		if ($this->AJAX === NULL OR defined('IS_AJAX') == FALSE)
		{
			if ( $this->EE->input->get_post('ajax') != FALSE OR (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') )
			{
				if (defined('IS_AJAX') == FALSE) define('IS_AJAX', TRUE);
				$this->AJAX = TRUE;
			}
			else
			{
				if (defined('IS_AJAX') == FALSE) define('IS_AJAX', FALSE);
				$this->AJAX = FALSE;
			}
		}

		return $this->AJAX;
	}

	// ********************************************************************************* //

	public function get_current_site_id()
	{
		if ($this->EE->input->cookie('cp_last_site_id')) $this->site_id = $this->EE->input->cookie('cp_last_site_id');
		else if ($this->EE->input->get_post('site_id')) $this->site_id = $this->EE->input->get_post('site_id');
		else $this->site_id = $this->EE->config->item('site_id');

		return $this->site_id;
	}

	// ********************************************************************************* //

	public function encrypt_string($string)
	{
		$this->EE->load->library('encrypt');
		if (function_exists('mcrypt_encrypt')) $this->EE->encrypt->set_cipher(MCRYPT_BLOWFISH);

		$string = $this->EE->encrypt->encode($string, substr(sha1(base64_encode(Forms_helper::$ekey)),0, 56));

		// Set it back
		if (function_exists('mcrypt_encrypt')) $this->EE->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);

		return $string;
	}

	// ********************************************************************************* //

	public function decode_string($string)
	{
		$this->EE->load->library('encrypt');
		if (function_exists('mcrypt_decrypt')) $this->EE->encrypt->set_cipher(MCRYPT_BLOWFISH);

		$string = $this->EE->encrypt->decode($string, substr(sha1(base64_encode(Forms_helper::$ekey)),0, 56));

		// Set it back
		if (function_exists('mcrypt_encrypt')) $this->EE->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);

		return $string;
	}

	// ********************************************************************************* //

	/**
	 * Get Upload Preferences (Cross-compatible between ExpressionEngine 2.0 and 2.4)
	 * @param  int $group_id Member group ID specified when returning allowed upload directories only for that member group
	 * @param  int $id       Specific ID of upload destination to return
	 * @return array         Result array of DB object, possibly merged with custom file upload settings (if on EE 2.4+)
	 */
	function get_upload_preferences($group_id = NULL, $id = NULL, $ignore_site_id = FALSE)
	{
		if (version_compare(APP_VER, '2.4', '>='))
		{
			$this->EE->load->model('file_upload_preferences_model');
			$row = $this->EE->file_upload_preferences_model->get_file_upload_preferences($group_id, $id, $ignore_site_id);
			$this->EE->session->cache['upload_prefs'][$id] = $row;
			return $row;
		}

		if (version_compare(APP_VER, '2.1.5', '>='))
		{
			// for admins, no specific filtering, just give them everything
			if ($group_id == 1)
			{
				// there a specific upload location we're looking for?
				if ($id != '')
				{
					$this->EE->db->where('id', $id);
				}

				$this->EE->db->from('upload_prefs');
				if ($ignore_site_id != TRUE) $this->EE->db->where('site_id', $this->EE->config->item('site_id'));
				$this->EE->db->order_by('name');

				$result = $this->EE->db->get();
			}
			else
			{
				// non admins need to first be checked for restrictions
				// we'll add these into a where_not_in() check below
				$this->EE->db->select('upload_id');
				$no_access = $this->EE->db->get_where('upload_no_access', array('member_group'=>$group_id));

				if ($no_access->num_rows() > 0)
				{
					$denied = array();
					foreach($no_access->result() as $result)
					{
						$denied[] = $result->upload_id;
					}
					$this->EE->db->where_not_in('id', $denied);
				}

				// there a specific upload location we're looking for?
				if ($id)
				{
					$this->EE->db->where('id', $id);
				}

				$this->EE->db->from('upload_prefs');
				if ($ignore_site_id != TRUE) $this->EE->db->where('site_id', $this->EE->config->item('site_id'));
				$this->EE->db->order_by('name');

				$result = $this->EE->db->get();
			}
		}
		else
		{
			$this->EE->load->model('tools_model');
			$result = $this->EE->tools_model->get_upload_preferences($group_id, $id);
		}

		// If an $id was passed, just return that directory's preferences
		if ( ! empty($id))
		{
			$result = $result->row_array();
			$this->EE->session->cache['upload_prefs'][$id] = $result;
			return $result;
		}

		// Use upload destination ID as key for row for easy traversing
		$return_array = array();
		foreach ($result->result_array() as $row)
		{
			$return_array[$row['id']] = $row;
		}

		$this->EE->session->cache['upload_prefs'][$id] = $return_array;

		return $return_array;
	}

	// ********************************************************************************* //

	public function uuid($dashes=true)
	{
		$id = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		    mt_rand( 0, 0x0fff ) | 0x4000,
		    mt_rand( 0, 0x3fff ) | 0x8000,
		    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );

		if ($dashes == false) $id = str_replace('-', '', $id);

		return $id;
	}

	// ********************************************************************************* //

	/**
	 * Generate new XID
	 *
	 * @return string the_xid
	 */
	public function xid_generator()
	{
		// Maybe it's already been made by EE
		if (defined('XID_SECURE_HASH') == TRUE) return XID_SECURE_HASH;

		// First is secure_forum enabled?
		if ($this->EE->config->item('secure_forms') == 'y')
		{
			// Did we already cache it?
			if (isset($this->EE->session->cache['XID']) == TRUE && $this->EE->session->cache['XID'] != FALSE)
			{
				return $this->EE->session->cache['XID'];
			}

			// Is there one already made that i can use?
			$this->EE->db->select('hash');
			$this->EE->db->from('exp_security_hashes');
			$this->EE->db->where('ip_address', $this->EE->input->ip_address());
			$this->EE->db->where('`date` > UNIX_TIMESTAMP()-3600');
			$this->EE->db->limit(1);
			$query = $this->EE->db->get();

			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				$this->EE->session->cache['XID'] = $row->hash;
				return $this->EE->session->cache['XID'];
			}

			// Lets make one then!
			$XID	= $this->EE->functions->random('encrypt');
			$this->EE->db->insert('exp_security_hashes', array('date' => $this->EE->localize->now, 'ip_address' => $this->EE->input->ip_address(), 'hash' => $XID));

			// Remove Old
			//$DB->query("DELETE FROM exp_security_hashes WHERE date < UNIX_TIMESTAMP()-7200"); // helps garbage collection for old hashes

			$this->EE->session->cache['XID'] = $XID;
			return $XID;
		}
	}

	// ********************************************************************************* //

	public function generate_json($obj)
	{
		if (function_exists('json_encode') === FALSE)
		{
			if (class_exists('Services_JSON_CUSTOM') === FALSE) include dirname(__FILE__).'/JSON.php';
			$JSON = new Services_JSON_CUSTOM();
			return $JSON->encode($obj);
		}
		else
		{
			return json_encode($obj);
		}
	}

	// ********************************************************************************* //

	public function decode_json($obj)
	{
		if (function_exists('json_decode') === FALSE)
		{
			if (class_exists('Services_JSON_CUSTOM') === FALSE) include dirname(__FILE__).'/JSON.php';
			$JSON = new Services_JSON_CUSTOM();
			return $JSON->decode($obj);
		}
		else
		{
			return json_decode($obj);
		}
	}

	// ********************************************************************************* //

	/**
	 * Array split - Split array in $pieces
	 *
	 * @param array
	 * @param int - Number of pieces
	 * @return array - Split array (multidimensional)
	 */
	function array_split($array, $pieces=2)
	{
		// Less then 2 pieces?
		if ($pieces < 2)
		{
			return array($array);
		}

		$newCount = ceil(count($array)/$pieces);

		$a = array_slice($array, 0, $newCount);

		$b = $this->array_split(array_slice($array, $newCount), $pieces-1);

		return array_merge(array($a),$b);
	}

	// ********************************************************************************* //

	/**
	 * Array Extend
	 * "Extend" recursively array $a with array $b values (no deletion in $a, just added and updated values)
	 * @param array $a
	 * @param array $b
	 */
	function array_extend($a, $b) {
		foreach($b as $k=>$v) {
			if( is_array($v) ) {
				if( !isset($a[$k]) ) {
					$a[$k] = $v;
				} else {
					$a[$k] = $this->array_extend($a[$k], $v);
				}
			} else {
				$a[$k] = $v;
			}
		}
		return $a;
	}

	// ********************************************************************************* //

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_number($str)
	{
   		return (bool)preg_match( '/^[0-9]+$/', $str);
	}

	// ********************************************************************************* //

	function parse_keywords($str, $remove=array())
	{
		// Remove all whitespace except single space
		$str = preg_replace("/(\r\n|\r|\n|\t|\s)+/", ' ', $str);

		// Characters that we do not want to allow...ever.
		// In the EE cleaner, we lost too many characters that might be useful in a Custom Field search, especially with Exact Keyword searches
		// The trick, security-wise, is to make sure any keywords output is converted to entities prior to any possible output
		$chars = array(	'{'	,
						'}'	,
						"^"	,
						"~"	,
						"*"	,
						"|"	,
						"["	,
						"]"	,
						'?'.'>'	,
						'<'.'?' ,
					  );

		// Keep as a space, helps prevent string removal security holes
		$str = str_replace(array_merge($chars, $remove), ' ', $str);

		// Only a single single space for spaces
		$str = preg_replace("/\s+/", ' ', $str);

		// Kill naughty stuff
		$str = trim($this->EE->security->xss_clean($str));

		return $str;
	}

	// ********************************************************************************* //

	/**
	 * Fetch URL with file_get_contents or with CURL
	 *
	 * @param string $url
	 * @return mixed
	 */
	function fetch_url_file($url, $user=false, $pass=false)
	{
		$data = '';

		/** --------------------------------------------
		/**  file_get_contents()
		/** --------------------------------------------*/

		if ((bool) @ini_get('allow_url_fopen') !== FALSE && $user == FALSE)
		{
			if ($data = @file_get_contents($url))
			{
				return trim($data);
			}
		}

		/** --------------------------------------------
		/**  cURL
		/** --------------------------------------------*/

		if (function_exists('curl_init') === TRUE && ($ch = @curl_init()) !== FALSE)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5 (.NET CLR 3.5.30729)');

			if (!ini_get('safe_mode') && !ini_get('open_basedir'))
			{
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			}

			if ($user != FALSE)
			{
				curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
				if (defined('CURLOPT_HTTPAUTH')) curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			}

			$data = curl_exec($ch);
			curl_close($ch);

			if ($data !== FALSE)
			{
				return trim($data);
			}
		}

		/** --------------------------------------------
        /**  fsockopen() - Last but only slightly least...
        /** --------------------------------------------*/

		$parts	= parse_url($url);
		$host	= $parts['host'];
		$path	= (!isset($parts['path'])) ? '/' : $parts['path'];
		$port	= ($parts['scheme'] == "https") ? '443' : '80';
		$ssl	= ($parts['scheme'] == "https") ? 'ssl://' : '';

		if (isset($parts['query']) && $parts['query'] != '')
		{
			$path .= '?'.$parts['query'];
		}

		$fp = @fsockopen($ssl.$host, $port, $error_num, $error_str, 7);

		if (is_resource($fp))
		{
			fputs ($fp, "GET ".$path." HTTP/1.0\r\n" );
			fputs ($fp, "Host: ".$host . "\r\n" );
			fputs ($fp, "User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1)\r\n");

			if ($user != FALSE)
			{
				fputs ($fp, "Authorization: Basic ".base64_encode("$user:$pass")."\r\n");
			}

			fputs ($fp, "Connection: close\r\n\r\n");

			$header = '';
			$body   = '';

			/* ------------------------------
			/*  This error suppression has to do with a PHP bug involving
			/*  SSL connections: http://bugs.php.net/bug.php?id=23220
			/* ------------------------------*/

			$old_level = error_reporting(0);

			/*
			while ( ! feof($fp))
			{
				$data .= trim(fgets($fp, 128));
			}
			*/

			// put the header in variable $header
			do // loop until the end of the header
			{
				$header .= fgets ( $fp, 128 );

			} while ( strpos ( $header, "\r\n\r\n" ) === false );

			// now put the body in variable $body
			while ( ! feof ( $fp ) )
			{
				$body .= fgets ( $fp, 128 );
			}

			error_reporting($old_level);

			$data = $body;

			fclose($fp);
		}

		return trim($data);
	}

	// ********************************************************************************* //

    /**
     * Function for looking for a value in a multi-dimensional array
     *
     * @param string $value
     * @param array $array
     * @return bool
     */
	function in_multi_array($value, $array)
	{
		foreach ($array as $key => $item)
		{
			// Item is not an array
			if (!is_array($item))
			{
				// Is this item our value?
				if ($item == $value) return TRUE;
			}

			// Item is an array
			else
			{
				// See if the array name matches our value
				//if ($key == $value) return true;

				// See if this array matches our value
				if (in_array($value, $item)) return TRUE;

				// Search this array
				else if ($this->in_multi_array($value, $item)) return TRUE;
			}
		}

		// Couldn't find the value in array
		return FALSE;
	}

	// ********************************************************************************* //

	/**
	 * Get Entry_ID from tag paramaters
	 *
	 * Supports: entry_id="", url_title="", channel=""
	 *
	 * @return mixed - INT or BOOL
	 */
	function get_entry_id_from_param($get_channel_id=FALSE)
	{
		$entry_id = FALSE;
		$channel_id = FALSE;

		$this->EE->load->helper('number');

		if ($this->EE->TMPL->fetch_param('entry_id') != FALSE && $this->is_natural_number($this->EE->TMPL->fetch_param('entry_id')) != FALSE)
		{
			$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		}
		elseif ($this->EE->TMPL->fetch_param('url_title') != FALSE)
		{
			$channel = FALSE;
			$channel_id = FALSE;

			if ($this->EE->TMPL->fetch_param('channel') != FALSE)
			{
				$channel = $this->EE->TMPL->fetch_param('channel');
			}

			if ($this->EE->TMPL->fetch_param('channel_id') != FALSE && $this->is_natural_number($this->EE->TMPL->fetch_param('channel_id')))
			{
				$channel_id = $this->EE->TMPL->fetch_param('channel_id');
			}

			$this->EE->db->select('exp_channel_titles.entry_id');
			$this->EE->db->select('exp_channel_titles.channel_id');
			$this->EE->db->from('exp_channel_titles');
			if ($channel) $this->EE->db->join('exp_channels', 'exp_channel_titles.channel_id = exp_channels.channel_id', 'left');
			$this->EE->db->where('exp_channel_titles.url_title', $this->EE->TMPL->fetch_param('url_title'));
			if ($channel) $this->EE->db->where('exp_channels.channel_name', $channel);
			if ($channel_id) $this->EE->db->where('exp_channel_titles.channel_id', $channel_id);
			$this->EE->db->limit(1);
			$query = $this->EE->db->get();

			if ($query->num_rows() > 0)
			{
				$channel_id = $query->row('channel_id');
				$entry_id = $query->row('entry_id');
				$query->free_result();
			}
			else
			{
				return FALSE;
			}
		}

		if ($get_channel_id != FALSE)
		{
			if ($this->EE->TMPL->fetch_param('channel') != FALSE)
			{
				$channel_id = $this->EE->TMPL->fetch_param('channel_id');
			}

			if ($channel_id == FALSE)
			{
				$this->EE->db->select('channel_id');
				$this->EE->db->where('entry_id', $entry_id);
				$this->EE->db->limit(1);
				$query = $this->EE->db->get('exp_channel_titles');
				$channel_id = $query->row('channel_id');

				$query->free_result();
			}

			$entry_id = array( 'entry_id'=>$entry_id, 'channel_id'=>$channel_id );
		}



		return $entry_id;
	}

	// ********************************************************************************* //

	/**
	 * Custom No_Result conditional
	 *
	 * Same as {if no_result} but with your own conditional.
	 *
	 * @param string $cond_name
	 * @param string $source
	 * @param string $return_source
	 * @return unknown
	 */
	public function custom_no_results_conditional($cond_name, $source, $return_source=FALSE)
	{
   		if (strpos($source, LD."if {$cond_name}".RD) !== FALSE)
		{
			if (preg_match('/'.LD."if {$cond_name}".RD.'(.*?)'.LD.'\/'.'if'.RD.'/s', $source, $cond))
			{
				return $cond[1];
			}

		}

		if ($return_source !== FALSE)
		{
			return $source;
		}

		return;
    }

	// ********************************************************************************* //

	/**
	 * Fetch data between var pairs
	 *
	 * @param string $open - Open var (with optional parameters)
	 * @param string $close - Closing var
	 * @param string $source - Source
	 * @return string
	 */
    function fetch_data_between_var_pairs($varname='', $source = '')
    {
    	if ( ! preg_match('/'.LD.($varname).RD.'(.*?)'.LD.'\/'.$varname.RD.'/s', $source, $match))
               return;

        return $match['1'];
    }

	// ********************************************************************************* //

	/**
	 * Fetch data between var pairs (including optional parameters)
	 *
	 * @param string $open - Open var (with optional parameters)
	 * @param string $close - Closing var
	 * @param string $source - Source
	 * @return string
	 */
    function fetch_data_between_var_pairs_params($open='', $close='', $source = '')
    {
    	if ( ! preg_match('/'.LD.preg_quote($open).'.*?'.RD.'(.*?)'.LD.'\/'.$close.RD.'/s', $source, $match))
               return;

        return $match['1'];
    }

	// ********************************************************************************* //

	/**
	 * Replace var_pair with final value
	 *
	 * @param string $open - Open var (with optional parameters)
	 * @param string $close - Closing var
	 * @param string $replacement - Replacement
	 * @param string $source - Source
	 * @return string
	 */
	function swap_var_pairs($varname = '', $replacement = '\\1', $source = '')
    {
    	$replacement = str_replace('$', '\\$', $replacement);
    	return preg_replace("/".LD.$varname.RD."(.*?)".LD.'\/'.$varname.RD."/s", $replacement, $source);
    }

	// ********************************************************************************* //

	/**
	 * Replace var_pair with final value (including optional parameters)
	 *
	 * @param string $open - Open var (with optional parameters)
	 * @param string $close - Closing var
	 * @param string $replacement - Replacement
	 * @param string $source - Source
	 * @return string
	 */
	function swap_var_pairs_params($open = '', $close = '', $replacement = '\\1', $source = '')
    {
    	$replacement = str_replace('$', '\\$', $replacement);
    	return preg_replace("/".LD.preg_quote($open).RD."(.*?)".LD.'\/'.$close.RD."/s", $replacement, $source);
    }

	// ********************************************************************************* //

    public function formatDate($format='', $date=0, $localize=true)
    {
    	if (method_exists($this->EE->localize, 'format_date') === true) {
    		return $this->EE->localize->format_date($format, $date, $localize);
    	} else {
    		return $this->EE->localize->decode_date($format, $date, $localize);
    	}
    }

	// ********************************************************************************* //

	public function mcp_meta_parser($type='js', $url, $name, $package='')
	{
		// -----------------------------------------
		// CSS
		// -----------------------------------------
		if ($type == 'css')
		{
			if ( isset($this->EE->session->cache['DevDemon']['CSS'][$name]) == FALSE )
			{
				$this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $url . '" type="text/css" media="print, projection, screen" />');
				$this->EE->session->cache['DevDemon']['CSS'][$name] = TRUE;
			}
		}

		// -----------------------------------------
		// Javascript
		// -----------------------------------------
		if ($type == 'js')
		{
			if ( isset($this->EE->session->cache['DevDemon']['JS'][$name]) == FALSE )
			{
				$this->EE->cp->add_to_foot('<script src="' . $url . '" type="text/javascript"></script>');
				$this->EE->session->cache['DevDemon']['JS'][$name] = TRUE;
			}
		}

		// -----------------------------------------
		// Global Inline Javascript
		// -----------------------------------------
		if ($type == 'gjs')
		{
			if ( isset($this->EE->session->cache['DevDemon']['GJS'][$name]) == FALSE )
			{
				$xid = $this->xid_generator();
				$AJAX_url = $this->get_router_url();
				$THEME_url = $this->define_theme_url();
				$MCP_BASE = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=forms';

				$js = "
						var Forms = Forms ? Forms : {};
						Forms.XID = '{$xid}';
						Forms.AJAX_URL = '{$AJAX_url}';
						Forms.THEME_URL = '{$THEME_url}';
						Forms.site_id = '{$this->site_id}';
						Forms.MCP_BASE = '{$MCP_BASE}';
					";

				$this->EE->cp->add_to_foot('<script type="text/javascript">' . $js . '</script>');
				$this->EE->session->cache['DevDemon']['GJS'][$name] = TRUE;
			}
		}
	}

} // END CLASS

/* End of file forms_helper.php  */
/* Location: ./system/expressionengine/third_party/forms/libraries/forms_helper.php */
