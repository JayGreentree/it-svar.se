<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Editor Module
 *
 * @package			DevDemon_Editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2012 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#core_module_file
 */
class Editor
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
		$this->EE->load->config('editor_config');
		$this->EE->load->library('editor_helper');
	}

	// ********************************************************************************* //

	public function ACT_file_upload()
	{
		$this->EE->load->library('firephp');
		$this->EE->load->library('filemanager');
		$this->EE->load->helper('directory');

		@header('Access-Control-Allow-Origin: *');
		@header('Access-Control-Allow-Credentials: true');
        @header('Access-Control-Max-Age: 86400');
        @header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        @header('Access-Control-Allow-Headers: Keep-Alive, Content-Type, User-Agent, Cache-Control, X-Requested-With, X-File-Name, X-File-Size');

        // -----------------------------------------
		// Increase all types of limits!
		// -----------------------------------------
		@set_time_limit(0);
		@ini_set('memory_limit', '64M');
		@ini_set('memory_limit', '96M');
		@ini_set('memory_limit', '128M');
		@ini_set('memory_limit', '160M');
		@ini_set('memory_limit', '192M');

		error_reporting(E_ALL);
		@ini_set('display_errors', 1);

		// -----------------------------------------
		// S3
		// -----------------------------------------
		if ($this->EE->input->get('bucket') != FALSE)
		{
			$bucket = $this->EE->input->get('bucket');
			$filename = $this->EE->input->get('key');
			exit(stripslashes('{ "filelink": "https://'.$bucket.'.s3.amazonaws.com/'.$filename.'", "filename": "'.$filename.'" }'));
		}

		if ($this->EE->input->get('action') == FALSE)
		{
			exit('{"error":"Missing Action or this is just an ACT URL test"}');
		}

		$action = $this->EE->input->get('action');
		if ($action == 'image_browser') $this->get_image_dir_json();


		// -----------------------------------------
		// Local File Upload
		// -----------------------------------------
		if ($this->EE->input->get_post('upload_location') === FALSE)
		{
			exit('{"error":"No Upload destination defined"}');
		}
		$location_id = $this->EE->input->get_post('upload_location');
		$location = $this->EE->editor_helper->get_upload_preferences(1, $location_id, TRUE);

		if ($action == 'image') $image_only = TRUE;
		else $image_only = FALSE;

		$file = $this->EE->filemanager->upload_file($location_id, 'file', $image_only);
		if (array_key_exists('error', $file)) {
			return '{"error":"'.strip_tags($file['error']).'"}';
		}

		//$this->EE->firephp->log($info);

		if ($action == 'file')
		{
			exit(stripslashes('{ "filelink": "'.$location['url'].$file['file_name'].'", "filename": "'.$file['file_name'].'" }'));
		}

		if ($action == 'image')
		{
			exit(stripslashes('{ "filelink": "'.$location['url'].$file['file_name'].'" }'));
		}


        exit('{"error":"Something went wrong"}');
	}

	// ********************************************************************************* //

	private function get_image_dir_json()
	{
		$this->EE->load->helper('directory');
		$location_id = $this->EE->input->get_post('upload_location');

		$location = $this->EE->editor_helper->get_upload_preferences(1, $location_id, TRUE);
		$location['server_path'] = preg_replace("/(.+?)\/*$/", "\\1/",  rtrim($location['server_path'], '\\/') ); // make sure the last slash is there

		$this->location = $location;
		$this->subdir = $this->EE->input->get_post('subdir');
		$this->allowed = array('jpg', 'jpeg', 'png', 'gif');
		$this->files = array();

		$files = array();

		if ($this->EE->input->get('subdir') == 'yes')
		{
			// Our Root Files First
			foreach(new DirectoryIterator($location['server_path']) as $file)
			{
				if ($file->isDot()) continue;
				if ($file->isFile())
				{
					$files[] = $file->getRealPath();
					continue;
				}
			}

			// Then our sub dirs! SUCKS!
			foreach(new DirectoryIterator($location['server_path']) as $file)
			{
				if ($file->isDot()) continue;
				if ($file->isDir())
				{
					foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file->getRealPath())) as $subfile)
					{
						if (strpos($subfile->getRealPath(), DIRECTORY_SEPARATOR.'_thumbs'.DIRECTORY_SEPARATOR) !== FALSE) continue;
						$files[] = $subfile->getRealPath();
					}
					continue;
				}
			}

			foreach($files as $path)
			{
				$path = str_replace(rtrim($location['server_path'], '\\/'), '', $path);
				$path = ltrim($path, '\\/');

				$ext = strtolower(substr( strrchr($path, '.'), 1));
				if (in_array($ext, $this->allowed) === FALSE) continue;

				$file = array();
				$file['thumb'] = $location['url'] . str_replace(DIRECTORY_SEPARATOR, '/', $path);
				$file['image'] = $location['url'] . str_replace(DIRECTORY_SEPARATOR, '/', $path);
				$file['title'] = ucfirst(str_replace('_', ' ', basename($path)));

				$subdir = '';
				$filename = $path;

				// Directory
				$file['folder'] = $location['name'];

				if (strpos($path, DIRECTORY_SEPARATOR) !== FALSE)
				{
					$path = explode(DIRECTORY_SEPARATOR, $path);
					$filename = array_pop($path);

					$subdir = implode(DIRECTORY_SEPARATOR, $path).DIRECTORY_SEPARATOR;
					$file['folder'] .= DIRECTORY_SEPARATOR.$subdir;
				}

				// Does the thumb version exists?
				if (file_exists($location['server_path'].$subdir.'_thumbs/'.$filename) === TRUE)
				{
					$file['thumb'] = $location['url'].$subdir.'_thumbs/'.$filename;
				}

				$this->files[] = $file;
			}
		}
		else
		{
			$map = directory_map($location['server_path'], 1);

			foreach ($map as $filename)
			{
				if (is_file($location['server_path'].$filename) === FALSE) continue;

				$ext = strtolower(substr( strrchr($filename, '.'), 1));
				if (in_array($ext, $this->allowed) === FALSE) continue;

				$file = array();
				$file['thumb'] = $location['url'] . $filename;
				$file['image'] = $location['url'] . $filename;
				$file['title'] = ucfirst(str_replace('_', ' ', str_replace($ext, '', $filename)));

				// Does the thumb version exists?
				if (file_exists($location['server_path'].'_thumbs/'.$filename) === TRUE)
				{
					$file['thumb'] = $location['url'].'_thumbs/'.$filename;
				}

				$this->files[] = $file;
			}
		}



		/*
		//$this->EE->firephp->log($location['server_path']);
		$map = directory_map($location['server_path'], 0);

		foreach ($map as $key => $item)
		{
			$temp = $item;
			if (is_array($item) === TRUE) $temp = $key.'/';

			$this->_parse_dir_json($location['server_path'].$temp);
		}
		*/

		//exit(print_r($this->files));
		exit($this->EE->editor_helper->generate_json($this->files));
	}

	// ********************************************************************************* //
/*
	private function _parse_dir_json($path)
	{
		if (is_dir($path) === TRUE)
		{
			if ($this->subdir == 'yes')
			{
				$map = directory_map($path, 0);

				foreach ($map as $key => $item)
				{
					$temp = $item;
					if (is_array($item) === TRUE) $temp = $key.'/';

					$this->_parse_dir_json($path.$temp);
				}
			}

			return;
		}

		if ($path == FALSE) return;

		$path = str_replace($this->location['server_path'], '', $path);
		//var_dump($path);

		$ext = strtolower(substr( strrchr($path, '.'), 1));
		if (in_array($ext, $this->allowed) === FALSE) return;

		$file = array();
		$file['thumb'] = $this->location['url'] . $path;
		$file['image'] = $this->location['url'] . $path;
		$file['title'] = rtrim(ucfirst(str_replace('_', ' ', str_replace($ext, '', $path))), '.');

		$subdir = '';
		$filename = $path;

		if ($this->subdir == 'yes')
		{
			// Directory
			$file['folder'] = $this->location['name'];

			if (strpos($path, '/') !== FALSE)
			{
				$path = explode('/', $path);
				$filename = array_pop($path);

				$subdir = implode('/', $path) . '/';
				$file['folder'] .= '/'.$subdir;
			}
		}

		// Does the thumb version exists?
		if (file_exists($this->location['server_path'].$subdir.'_thumbs/'.$filename) === TRUE)
		{
			$file['thumb'] = $this->location['url'].$subdir.'_thumbs/'.$filename;
		}

		$this->files[] = $file;
	}
*/
	// ********************************************************************************* //


} // END CLASS

/* End of file mod.updater.php */
/* Location: ./system/expressionengine/third_party/updater/mod.updater.php */
