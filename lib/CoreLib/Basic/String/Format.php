<?php
namespace Basic\String;

class Format extends \Core\Object {
	/**
	 * version of sprintf for cases where named arguments are desired (python syntax)
	 *
	 * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
	 *
	 * with sprintfn: sprintfn('second: %(second)s ; first: %(first)s', array(
	 *  'first' => '1st',
	 *  'second'=> '2nd'
	 * ));
	 *
	 * @param string $format sprintf format string, with any number of named arguments
	 * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
	 * @return string|false result of sprintf call, or bool false on error
	 */
	static function sprintfn($format, array $args = array()) {
		// map of argument names to their corresponding sprintf numeric argument
		// value
		$arg_nums = array_slice ( array_flip ( array_keys ( array (
				0 => 0 
		) + $args ) ), 1 );
		
		// find the next named argument. each search starts at the end of the
		// previous replacement.
		for($pos = 0; preg_match ( '/(?<=%)\(([a-zA-Z_]\w*)\)/', $format, $match, PREG_OFFSET_CAPTURE, $pos );) {
			$arg_pos = $match [0] [1];
			$arg_len = strlen ( $match [0] [0] );
			$arg_key = $match [1] [0];
			
			// programmer did not supply a value for the named argument found in
			// the format string
			if (! array_key_exists ( $arg_key, $arg_nums )) {
				user_error ( "sprintfn(): Missing argument '${arg_key}'", E_USER_WARNING );
				return false;
			}
			
			// replace the named argument with the corresponding numeric one
			$format = substr_replace ( $format, $replace = $arg_nums [$arg_key] . '$', $arg_pos, $arg_len );
			$pos = $arg_pos + strlen ( $replace ); // skip to end of replacement for
			                                    // next iteration
		}
		
		return vsprintf ( $format, array_values ( $args ) );
	}
	
	/**
	 * version of sscanf for cases where named arguments are desired (python syntax)
	 * if no match is found, false is returned.
	 *
	 * sscanfn('second: %(second)s ; first: %(first)s', array(
	 *  'first' => '1st',
	 *  'second'=> '2nd'
	 * ));
	 *
	 * @param string $str string to match on
	 * @param string $format sprintf format string, with any number of named arguments
	 * @return array|bool result of scanf call with arguments as their key or false if no match
	 */
	static function sscanfn($str, $format) {	
		$array_return = array();
		// find the next named argument. each search starts at the end of the
		// previous replacement.
		for($pos = 0; preg_match ( '/(?<=%)\(([a-zA-Z_]\w*)\)/', $format, $match, PREG_OFFSET_CAPTURE, $pos );) {
			$arg_pos = $match [0] [1];
			$arg_len = strlen ( $match [0] [0] );
			$arg_key = $match [1] [0];
			$array_return[] = $arg_key;
			
			// replace the named argument with the corresponding numeric one
			$format = substr_replace ( $format, '', $arg_pos, $arg_len );
			// next iteration
		}
	
		$ret = array();
		foreach(sscanf($str, $format) as $num=>$value){
			if($value === NULL) return false;
			$ret[$array_return[$num]] = $value;
		}
		
		return $ret;
	}
	
	/**
	 * version of sscanf for cases where named arguments are desired (python syntax)
	 *
	 * Consume('second: %(second)s ; first: %(first)s', array(
	 *  'first' => '1st',
	 *  'second'=> '2nd'
	 * ));
	 *
	 * @param string $str string to match on
	 * @param string $format sprintf format string, with any number of named arguments
	 * @return array result of scanf call with arguments as their key
	 */
	static function Consume($str,$format){
		return static::sscanfn($str, $format);
	}
	
	/**
	 * version of sprintf for cases where named arguments are desired (python syntax)
	 *
	 * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
	 *
	 * with sprintfn: sprintfn('second: %(second)s ; first: %(first)s', array(
	 *  'first' => '1st',
	 *  'second'=> '2nd'
	 * ));
	 *
	 * @param string $format sprintf format string, with any number of named arguments
	 * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
	 * @return string|false result of sprintf call, or bool false on error
	 */
	static function Format($format, array $args = array()){
		return static::sprintfn($format,$args);
	}
}