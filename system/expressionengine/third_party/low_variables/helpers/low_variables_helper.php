<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Variables helper functions
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

// --------------------------------------------------------------------

/**
 * Encode array to string
 *
 * @param      array     Array to encode
 * @return     string
 */
if ( ! function_exists('low_array_encode'))
{
	function low_array_encode($array = array())
	{
		return str_replace('/', '_', rtrim(base64_encode(serialize($array)), '='));
	}
}

// --------------------------------------------------------------------

/**
 * Decode string back to array
 *
 * @param      string    String to decode
 * @return     array
 */
if ( ! function_exists('low_array_decode'))
{
	function low_array_decode($str = '')
	{
		return (is_string($str) && strlen($str)) ? @unserialize(base64_decode(str_replace('_', '/', $str))) : FALSE;
	}
}

// --------------------------------------------------------------------

/**
 * Converts EE parameter to workable php vars
 *
 * @access     private
 * @param      string    String like 'not 1|2|3' or '40|15|34|234'
 * @return     array     [0] = array of ids, [1] = boolean whether to include or exclude: TRUE means include, FALSE means exclude
 */
if ( ! function_exists('low_explode_param'))
{
	function low_explode_param($str)
	{
		// --------------------------------------
		// Initiate $in var to TRUE
		// --------------------------------------

		$in = TRUE;

		// --------------------------------------
		// Check if parameter is "not bla|bla"
		// --------------------------------------

		if (strtolower(substr($str, 0, 4)) == 'not ')
		{
			// Change $in var accordingly
			$in = FALSE;

			// Strip 'not ' from string
			$str = substr($str, 4);
		}

		// --------------------------------------
		// Return two values in an array
		// --------------------------------------

		return array(preg_split('/(&&?|\|)/', $str), $in);
	}
}

// --------------------------------------------------------------------

/**
 * Flatten results
 *
 * Given a DB result set, this will return an (associative) array
 * based on the keys given
 *
 * @param      array
 * @param      string    key of array to use as value
 * @param      string    key of array to use as key (optional)
 * @return     array
 */
if ( ! function_exists('low_flatten_results'))
{
	function low_flatten_results($resultset, $val, $key = FALSE)
	{
		$array = array();

		foreach ($resultset AS $row)
		{
			if ($key !== FALSE)
			{
				$array[$row[$key]] = $row[$val];
			}
			else
			{
				$array[] = $row[$val];
			}
		}

		return $array;
	}
}

// --------------------------------------------------------------------

/**
 * Associate results
 *
 * Given a DB result set, this will return an (associative) array
 * based on the keys given
 *
 * @param      array
 * @param      string    key of array to use as key
 * @param      bool      sort by key or not
 * @return     array
 */
if ( ! function_exists('low_associate_results'))
{
	function low_associate_results($resultset, $key, $sort = FALSE)
	{
		$array = array();

		foreach ($resultset AS $row)
		{
			if (array_key_exists($key, $row) && ! array_key_exists($row[$key], $array))
			{
				$array[$row[$key]] = $row;
			}
		}

		if ($sort === TRUE)
		{
			ksort($array);
		}

		return $array;
	}
}

// --------------------------------------------------------------

/**
 * Is current request an Ajax request or not?
 *
 * @return     bool
 */
if ( ! function_exists('is_ajax'))
{
	function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
	}
}

// --------------------------------------------------------------

/**
 * Is array numeric; filled with numeric values?
 *
 * @param      array
 * @return     bool
 */
if ( ! function_exists('low_array_is_numeric'))
{
	function low_array_is_numeric($array = array())
	{
		$numeric = TRUE;

		foreach ($array AS $val)
		{
			if ( ! is_numeric($val))
			{
				$numeric = FALSE;
				break;
			}
		}

		return $numeric;
	}
}

// --------------------------------------------------------------

/**
 * Get cache value, either using the cache method (EE2.2+) or directly from cache array
 *
 * @param       string
 * @param       string
 * @return      mixed
 */
if ( ! function_exists('low_get_cache'))
{
	function low_get_cache($a, $b)
	{
		if (method_exists(ee()->session, 'cache'))
		{
			return ee()->session->cache($a, $b);
		}
		else
		{
			return (isset(ee()->session->cache[$a][$b]) ? ee()->session->cache[$a][$b] : FALSE);
		}
	}
}

// --------------------------------------------------------------

/**
 * Set cache value, either using the set_cache method (EE2.2+) or directly to cache array
 *
 * @param       string
 * @param       string
 * @param       mixed
 * @return      void
 */
if ( ! function_exists('low_set_cache'))
{
	function low_set_cache($a, $b, $c)
	{
		if (method_exists(ee()->session, 'set_cache'))
		{
			ee()->session->set_cache($a, $b, $c);
		}
		else
		{
			ee()->session->cache[$a][$b] = $c;
		}
	}
}

// --------------------------------------------------------------

/**
 * Zebra table helper
 *
 * @param       bool
 * @return      string
 */
if ( ! function_exists('low_zebra'))
{
	function low_zebra($reset = FALSE)
	{
		static $i = 0;

		if ($reset) $i = 0;

		return (++$i % 2 ? 'odd' : 'even');
	}
}

// --------------------------------------------------------------

/**
 * Debug
 *
 * @param       mixed
 * @param       bool
 * @return      void
 */
if ( ! function_exists('low_dump'))
{
	function low_dump($var, $exit = TRUE)
	{
		echo '<pre>'.htmlentities(print_r($var, TRUE)).'</pre>';
		if ($exit) exit;
	}
}

// --------------------------------------------------------------

/* End of file low_variables_helper.php */