<?php
namespace Basic;

/**
 * Array helper.
 *
 * Based on code from Kohana
 */
class Arr {
	protected static function _is_accessible($in){
		if(is_array($in)){
			return true;
		}
		if(is_object($in) && $in instanceof \ArrayAccess){
			return true;
		}
		return false;
	}
	static function toArray($in){
		if(is_object($in)){
			if($in instanceof Arr\Object\CollectionObject){
				return $in->toArray();
			}elseif($in instanceof \Traversable){
				$ret = array();
				foreach($in as $k=>$v){
					$ret[$k] = $v;
				}
				return $ret;
			}
		}
		return false;
	}
	protected static function parameterHandle(&$p){
		if(self::_is_accessible($p)){
			return;
		}
		$r = self::toArray($p);
		if($r){
			$p = $r;
			return;
		}
		throw new \BadMethodCallException('Parameter must be an array');
	}
	
	/**
	 * @var  string  default delimiter for path()
	 */
	public static $delimiter = '.';

	/**
	 * Tests if an array is associative or not.
	 *
	 *     // Returns TRUE
	 *     Arr::is_assoc(array('username' => 'john.doe'));
	 *
	 *     // Returns FALSE
	 *     Arr::is_assoc('foo', 'bar');
	 *
	 * @param   array   array to check
	 * @return  boolean
	 */
	public static function is_assoc($array)
	{
		self::parameterHandle($array);
		
		//Handle as object
		if(is_object($array)){
			if($array instanceof Arr\Object\CollectionObject){
				return $array->isAssoc();
			}
			$array = self::toArray($array);
		}
		
		// Keys of the array
		$keys = array_keys($array);

		// If the array keys of the keys match the keys, then the array must
		// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
		return array_keys($keys) !== $keys;
	}

	/**
	 * Test if a value is an array with an additional check for array-like objects.
	 *
	 *     // Returns TRUE
	 *     Arr::is_array(array());
	 *     Arr::is_array(new ArrayObject);
	 *
	 *     // Returns FALSE
	 *     Arr::is_array(FALSE);
	 *     Arr::is_array('not an array!');
	 *     Arr::is_array(Database::instance());
	 *
	 * @param   mixed    value to check
	 * @return  boolean
	 */
	public static function is_array($value)
	{
		if (self::_is_accessible($value))
		{
			// An accessible array type
			return true;
		}
		else
		{
			// Possibly a Traversable object, functionally the same as an array
			return (is_object($value) AND $value instanceof \Traversable);
		}
	}

	

	/**
	 * Fill an array with a range of numbers.
	 *
	 *     // Fill an array with values 5, 10, 15, 20
	 *     $values = Arr::range(5, 20);
	 *
	 * @param   integer  stepping
	 * @param   integer  ending number
	 * @return  array
	 */
	public static function range($step = 10, $max = 100)
	{
		if ($step < 1)
			return array();

		$array = array();
		for ($i = $step; $i <= $max; $i += $step)
		{
			$array[$i] = $i;
		}

		return $array;
	}

	/**
	 * Retrieve a single key from an array. If the key does not exist in the
	 * array, the default value will be returned instead.
	 *
	 *     // Get the value "username" from $_POST, if it exists
	 *     $username = Arr::get($_POST, 'username');
	 *
	 *     // Get the value "sorting" from $_GET, if it exists
	 *     $sorting = Arr::get($_GET, 'sorting');
	 *
	 * @param   array   array to extract from
	 * @param   string  key name
	 * @param   mixed   default value
	 * @return  mixed
	 */
	public static function get($array, $key, $default = NULL)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}

	/**
	 * Retrieves multiple keys from an array. If the key does not exist in the
	 * array, the default value will be added instead.
	 *
	 *     // Get the values "username", "password" from $_POST
	 *     $auth = Arr::extract($_POST, array('username', 'password'));
	 *
	 * @param   array   array to extract keys from
	 * @param   array   list of key names
	 * @param   mixed   default value
	 * @return  array
	 */
	public static function extract($array, array $keys, $default = NULL)
	{
		$found = array();
		foreach ($keys as $key)
		{
			$found[$key] = isset($array[$key]) ? $array[$key] : $default;
		}

		return $found;
	}

	/**
	 * Retrieves muliple single-key values from a list of arrays.
	 *
	 *     // Get all of the "id" values from a result
	 *     $ids = Arr::pluck($result, 'id');
	 *
	 * [!!] A list of arrays is an array that contains arrays, eg: array(array $a, array $b, array $c, ...)
	 *
	 * @param   array   list of arrays to check
	 * @param   string  key to pluck
	 * @return  array
	 */
	public static function pluck($array, $key)
	{
		$values = array();

		foreach ($array as $row)
		{
			if (isset($row[$key]))
			{
				// Found a value in this row
				$values[] = $row[$key];
			}
		}

		return $values;
	}

	/**
	 * Adds a value to the beginning of an associative array.
	 *
	 *     // Add an empty value to the start of a select list
	 *     Arr::unshift($array, 'none', 'Select a value');
	 *
	 * @param   array   array to modify
	 * @param   string  array key name
	 * @param   mixed   array value
	 * @return  array
	 */
	public static function unshift( array & $array, $key, $val)
	{
		$array = array_reverse($array, TRUE);
		$array[$key] = $val;
		$array = array_reverse($array, TRUE);

		return $array;
	}
	
	
	/**
	 * Can work like normal array_map except supports being passed array objects.
	 * 
	 * If the callback is an array where the first member is * then the callback 
	 * is applied using the array value::callack[1]()
	 * 
	 * @param mixed $callback
	 * @param array $array
	 * @return array
	 */
	public static function map($callback, $array)
	{
		if(is_object($array)){
			if($array instanceof \Iterator || $array instanceof \IteratorAggregate){
				$array = iterator_to_array($array);
			}
		}
		if(is_array($callback) && $callback[0] == '*'){
			$c = $callback[1];
			foreach($array as $k=>$v){
				$array[$k] = $v::$c();
			}
			return $array;
		}
		return array_map($callback,$array);
	}

	/**
	 * Recursive version of [array_map](http://php.net/array_map), applies the
	 * same callback to all elements in an array, including sub-arrays.
	 *
	 *     // Apply "strip_tags" to every element in the array
	 *     $array = Arr::map_recursive('strip_tags', $array);
	 *
	 * [!!] Unlike `array_map`, this method requires a callback and will only map
	 * a single array.
	 *
	 * @param   mixed   callback applied to every element in the array
	 * @param   array   array to map
	 * @param   array   array of keys to apply to
	 * @return  array
	 */
	public static function map_recursive($callback, $array, $keys = NULL)
	{
		foreach ($array as $key => $val)
		{
			if (self::is_array($val))
			{
				$array[$key] = Arr::map($callback, $array[$key]);
			}
			elseif ( ! is_array($keys) or in_array($key, $keys))
			{
				//Removed kohana array of callback support, its stupid... use closures
				$array[$key] = call_user_func($callback, $array[$key]);
			}
		}

		return $array;
	}

	/**
	 * Merges one or more arrays recursively and preserves all keys.
	 * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
	 *
	 *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
	 *     $mary = array('name' => 'mary', 'children' => array('jane'));
	 *
	 *     // John and Mary are married, merge them together
	 *     $john = Arr::merge($john, $mary);
	 *
	 *     // The output of $john will now be:
	 *     array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
	 *
	 * @param   array  initial array
	 * @param   array  array to merge
	 * @param   array  ...
	 * @return  array
	 */
	public static function merge(array $a1, array $a2)
	{
		$result = array();
		for ($i = 0, $total = func_num_args(); $i < $total; $i++)
		{
			// Get the next array
			$arr = func_get_arg($i);

			// Is the array associative?
			$assoc = Arr::is_assoc($arr);

			foreach ($arr as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val) AND is_array($result[$key]))
					{
						if (Arr::is_assoc($val))
						{
							// Associative arrays are merged recursively
							$result[$key] = Arr::merge($result[$key], $val);
						}
						else
						{
							// Find the values that are not already present
							$diff = array_diff($val, $result[$key]);

							// Indexed arrays are merged to prevent duplicates
							$result[$key] = array_merge($result[$key], $diff);
						}
					}
					else
					{
						if ($assoc)
						{
							// Associative values are replaced
							$result[$key] = $val;
						}
						elseif ( ! in_array($val, $result, TRUE))
						{
							// Indexed values are added only if they do not yet exist
							$result[] = $val;
						}
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}
	
	/**
	 * Form an associative array from a linear array.

	This function walks through the provided array and constructs an associative array out of it. The keys of the
	 resulting array will be the values of the input array. The values will be the same as the keys unless a 
	 function is specified, in which case the output of the function is used for the values instead.

	 * @param array $array linear array
	 * @param callback $function a callback function to apply
	 * @return array
	 */
	public static function map_assoc($array, $function = NULL) {
		$result = array();
		if ($function === null) {
			foreach ($array as $value) {
				$result[$value] = $value;
			}
		} elseif (is_callable($function)) {
			foreach ($array as $value) {
				$result[$value] = $function($value);
			}
		}else{
			throw new \Exception('$funcion must be a valid callback (must be callable)');
		}
		return $result;
	}

	/**
	 * Overwrites an array with values from input arrays.
	 * Keys that do not exist in the first array will not be added!
	 *
	 *     $a1 = array('name' => 'john', 'mood' => 'happy', 'food' => 'bacon');
	 *     $a2 = array('name' => 'jack', 'food' => 'tacos', 'drink' => 'beer');
	 *
	 *     // Overwrite the values of $a1 with $a2
	 *     $array = Arr::overwrite($a1, $a2);
	 *
	 *     // The output of $array will now be:
	 *     array('name' => 'jack', 'mood' => 'happy', 'food' => 'tacos')
	 *
	 * @param   array   master array
	 * @param   array   input arrays that will overwrite existing values
	 * @param	array	...
	 * @return  array
	 */
	public static function overwrite($array1, $array2)
	{
		//The work
		foreach (array_intersect_key($array2, $array1) as $key => $value)
		{
			$array1[$key] = $value;
		}

		//The ... parameter expansion
		if (func_num_args() > 2)
		{
			$args = array_slice(func_get_args(), 2);
			foreach($args as $v){
				$array1 = self::overwrite($array1, $v);
			}
		}

		return $array1;
	}

	/**
	 * Creates a callable function and parameter list from a string representation.
	 * Note that this function does not validate the callback string.
	 *
	 *     // Get the callback function and parameters
	 *     list($func, $params) = Arr::callback('Foo::bar(apple,orange)');
	 *
	 *     // Get the result of the callback
	 *     $result = call_user_func_array($func, $params);
	 *
	 * @param   string  callback string
	 * @return  array   function, params
	 */
	public static function callback($str)
	{
		// Overloaded as parts are found
		$command = $params = NULL;

		// command[param,param]
		if (preg_match('/^([^\(]*+)\((.*)\)$/', $str, $match))
		{
			// command
			$command = $match[1];

			if ($match[2] !== '')
			{
				// param,param
				$params = preg_split('/(?<!\\\\),/', $match[2]);
				$params = str_replace('\,', ',', $params);
			}
		}
		else
		{
			// command
			$command = $str;
		}

		if (strpos($command, '::') !== FALSE)
		{
			// Create a static method callable command
			$command = explode('::', $command, 2);
		}

		return array($command, $params);
	}

	/**
	 * Convert a multi-dimensional array into a single-dimensional array.
	 *
	 *     $array = array('set' => array('one' => 'something'), 'two' => 'other');
	 *
	 *     // Flatten the array
	 *     $array = Arr::flatten($array);
	 *
	 *     // The array will now be
	 *     array('one' => 'something', 'two' => 'other');
	 *
	 * [!!] The keys of array values will be discarded.
	 *
	 * @param   array   array to flatten
	 * @return  array
	 */
	public static function flatten($array)
	{
		$flat = array();
		foreach ($array as $key => $value)
		{
			if (self::is_array($value))
			{
				$flat += Arr::flatten($value);
			}
			else
			{
				$flat[$key] = $value;
			}
		}
		return $flat;
	}
	
	/**
	 * Return all values from an array where the callback function
	 * returns true. The function callback is called with the key 
	 * as the first parameter and the value as the second.
	 * 
	 * @param callback $callback
	 * @param array $array
	 * @return array
	 */
	static function where($callback,$array,$strict = false){
		$ret = array();
		foreach($array as $k=>$v){
			if(is_callable($callback) && !$strict){
				if($callback($k,$v)) $ret[$k] = $v;
			}elseif(($strict && $v === $value) || $v == $value){
				$ret[$k] = $v;
			}
		}
		return $ret;
	}

} // End arr