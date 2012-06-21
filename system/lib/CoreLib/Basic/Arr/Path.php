<?php
namespace Basic\Arr;

use Basic\Arr;

class Path {
	/**
	 * Gets a value from an array using a dot separated path.
	 *
	 *     // Get the value of $array['foo']['bar']
	 *     $value = Path::get($array, 'foo.bar');
	 *
	 * Using a wildcard "*" will search intermediate arrays and return an array.
	 *
	 *     // Get the values of "color" in theme
	 *     $colors = Path::get($array, 'theme.*.color');
	 *
	 *     // Using an array of keys
	 *     $colors = Path::get($array, array('theme', '*', 'color'));
	 *
	 * @param   array   array to search
	 * @param   mixed   key path string (delimiter separated) or array of keys
	 * @param   mixed   default value if the path is not set
	 * @param   string  key path delimiter
	 * @return  mixed
	 */
	public static function get($array, $path, $default = NULL, $delimiter = NULL)
	{
		if ( ! Arr::is_array($array))
		{
			// This is not an array!
			return $default;
		}
	
		if (is_array($path))
		{
			// The path has already been separated into keys
			$keys = $path;
		}
		else
		{
			if (array_key_exists($path, $array))
			{
				// No need to do extra processing
				return $array[$path];
			}
	
			if ($delimiter === NULL)
			{
				// Use the default delimiter
				$delimiter = Arr::$delimiter;
			}
	
			// Remove starting delimiters and spaces
			$path = ltrim($path, "{$delimiter} ");
	
			// Remove ending delimiters, spaces, and wildcards
			$path = rtrim($path, "{$delimiter} *");
	
			// Split the keys by delimiter
			$keys = explode($delimiter, $path);
		}
	
		do
		{
			$key = array_shift($keys);
	
			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}
	
			if (isset($array[$key]))
			{
				if ($keys)
				{
					if (Arr::is_array($array[$key]))
					{
						// Dig down into the next part of the path
						$array = $array[$key];
					}
					else
					{
						// Unable to dig deeper
						break;
					}
				}
				else
				{
					// Found the path requested
					return $array[$key];
				}
			}
			elseif ($key === '*')
			{
				// Handle wildcards
	
				$values = array();
				foreach ($array as $arr)
				{
					if ($value = Arr::path($arr, implode('.', $keys)))
					{
						$values[] = $value;
					}
				}
	
				if ($values)
				{
					// Found the values requested
					return $values;
				}
				else
				{
					// Unable to dig deeper
					break;
				}
			}
			else
			{
				// Unable to dig deeper
				break;
			}
		}
		while ($keys);
	
		// Unable to find the value requested
		return $default;
	}
	
	/**
	 * Set a value on an array by path.
	 *
	 * @see Arr::path()
	 * @param array   $array     Array to update
	 * @param string  $path      Path
	 * @param mixed   $value     Value to set
	 * @param string  $delimiter Path delimiter
	 */
	public static function set( & $array, $path, $value, $delimiter = NULL)
	{
		if ( ! $delimiter)
		{
			// Use the default delimiter
			$delimiter = Arr::$delimiter;
		}
	
		// Split the keys by delimiter
		$keys = explode($delimiter, $path);
	
		// Set current $array to inner-most array path
		while (count($keys) > 1)
		{
			$key = array_shift($keys);
	
			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}
	
			if ( ! isset($array[$key]))
			{
				$array[$key] = array();
			}
	
			$array = & $array[$key];
		}
	
		// Set key on inner-most array
		$array[array_shift($keys)] = $value;
	}
}