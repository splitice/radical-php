<?php
namespace Basic\String;
use Basic\Arr;

class Truncate {
	static function trim($value, $max_length, $is_html = false) {
		if (UTF8::len ( $value ) > $max_length) {
			$value = UTF8::sub ( $value, 0, $max_length );
			
			// TODO: replace this with cleanstring of ctools
			$regex = '(.*)\b.+';
			if (function_exists ( 'mb_ereg' )) {
				mb_regex_encoding ( 'UTF-8' );
				$found = mb_ereg ( $regex, $value, $matches );
			} else {
				$found = preg_match ( "/$regex/us", $value, $matches );
			}
			if ($found) {
				$value = $matches [1];
			}
			
			if ($is_html) {
				// Remove scraps of HTML entities from the end of a strings
				$regex = '/(?:<(?!.+>)|&(?!.+;)).*$/s';
				$value2 = preg_replace ( $regex.'u', '', $value );
				if(preg_last_error() == 4){
					$value = preg_replace ( $regex, '', $value );
				}else{
					$value = $value2;
				}
			}
			$value = rtrim ( $value );
			
			$value .= '...';
		}
		if ($is_html) {
			$value = self::_filter_htmlcorrector ( $value );
		}
		
		return $value;
	}
	private function _filter_htmlcorrector($text) {
		// Prepare tag lists.
		static $no_nesting, $single_use;
		if (! isset ( $no_nesting )) {
			// Tags which cannot be nested but are typically left unclosed.
			$no_nesting = Arr::map_assoc ( array (
					'li',
					'p' 
			) );
			
			// Single use tags in HTML4
			$single_use = Arr::map_assoc ( array (
					'base',
					'meta',
					'link',
					'hr',
					'br',
					'param',
					'img',
					'area',
					'input',
					'col',
					'frame' 
			) );
		}
		
		// Properly entify angles.
		$text = preg_replace ( '@<(?=[^a-zA-Z!/]|$)@', '&lt;', $text );
		
		// Split tags from text.
		$split = preg_split ( '/<(!--.*?--|[^>]+?)>/s', $text, - 1, PREG_SPLIT_DELIM_CAPTURE );
		// Note: PHP ensures the array consists of alternating delimiters and
		// literals
		// and begins and ends with a literal (inserting $null as required).
		
		$tag = false; // Odd/even counter. Tag or no tag.
		$stack = array ();
		$output = '';
		foreach ( $split as $value ) {
			// Process HTML tags.
			if ($tag) {
				// Passthrough comments.
				if (substr ( $value, 0, 3 ) == '!--') {
					$output .= '<' . $value . '>';
				} else {
					list ( $tagname ) = preg_split ( '/\s/', strtolower ( $value ), 2 );
					// Closing tag
					if ($tagname {0} == '/') {
						$tagname = substr ( $tagname, 1 );
						// Discard XHTML closing tags for single use tags.
						if (! isset ( $single_use [$tagname] )) {
							// See if we possibly have a matching opening tag on
							// the stack.
							if (in_array ( $tagname, $stack )) {
								// Close other tags lingering first.
								do {
									$output .= '</' . $stack [0] . '>';
								} while ( array_shift ( $stack ) != $tagname );
							}
							// Otherwise, discard it.
						}
					} 					// Opening tag
					else {
						// See if we have an identical 'no nesting' tag already
						// open and close it if found.
						if (count ( $stack ) && ($stack [0] == $tagname) && isset ( $no_nesting [$stack [0]] )) {
							$output .= '</' . array_shift ( $stack ) . '>';
						}
						// Push non-single-use tags onto the stack
						if (! isset ( $single_use [$tagname] )) {
							array_unshift ( $stack, $tagname );
						} 						// Add trailing slash to single-use tags as per X(HT)ML.
						else {
							$value = rtrim ( $value, ' /' ) . ' /';
						}
						$output .= '<' . $value . '>';
					}
				}
			} else {
				// Passthrough all text.
				$output .= $value;
			}
			$tag = ! $tag;
		}
		// Close remaining tags.
		while ( count ( $stack ) > 0 ) {
			$output .= '</' . array_shift ( $stack ) . '>';
		}
		return $output;
	}
}