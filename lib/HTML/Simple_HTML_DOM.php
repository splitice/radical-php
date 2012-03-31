<?php
namespace HTML;

class NodeNotFound extends \Exception {

}

// helper functions
// -----------------------------------------------------------------------------


function simple_html_dom_class() {
	/*if (class_exists ( 'DomDocument' )) {
		return new native_Simple_HTML_DOM ();
	}*/
	return new Simple_HTML_DOM ();
}

// get html dom form file
function file_get_html() {
	$dom = simple_html_dom_class ();
	$args = func_get_args ();
	$dom->load ( call_user_func_array ( 'file_get_contents', $args ), true );
	return $dom;
}

// get html dom form string
function str_get_html($str, $lowercase = true) {
	$dom = simple_html_dom_class ();
	$dom->load ( $str, $lowercase );
	return $dom;
}

// get dom form file (deprecated)
function file_get_dom() {
	$args = func_get_args ();
	return call_user_func_array ( '\\HTML\\file_get_html', $args );
}

// get dom form string (deprecated)
function str_get_dom($str, $lowercase = true) {
	return str_get_html ( $str, $lowercase );
}

// simple html dom node
// -----------------------------------------------------------------------------
class native_simple_html_dom_node {
	private $dom;
	private $document;
	private $root;
	
	function __construct($dom, $document, $root = false) {
		$this->dom = $dom;
		$this->document = $document;
		$this->root = $root;
	}
	
	function __toString() {
		return $this->outertext ();
	}
	
	// returns the parent of node
	function parent() {
		if(!$this->$this->dom->parentNode){
			return null;
		}
		return new native_simple_html_dom_node ( $this->dom->parentNode, $this->document );
	}
	
	// returns children of node
	function children($idx = -1) {
		if ($this->dom->childNodes) {
			$ret = array ();
			foreach ( $this->dom->childNodes as $c ) {
				$ret [] = new native_simple_html_dom_node ( $c, $this->document );
			}
			return $ret;
		}
	}
	
	// returns the first child of node
	function first_child() {
		if ($this->dom->childNodes) {
			return $this->dom->childNodes [0];
		}
	}
	
	// returns the last child of node
	function last_child() {
		if ($this->dom->childNodes) {
			$r = array_reverse ( $this->dom->childNodes );
			return $r [0];
		}
	}
	
	// returns the next sibling of node    
	function next_sibling() {
		if ($this->dom->nextSibling){
			$n = $this->dom;
			do {
				$n = $n->nextSibling;
			}while($n && $n->nodeType != XML_ELEMENT_NODE);
			if($n){
				return new native_simple_html_dom_node ( $n, $this->document );
			}
		}
	}
	
	// returns the previous sibling of node
	function prev_sibling() {
		if ($this->dom->prevSibling){
			$n = $this->dom;
			do {
				$n = $n->prevSibling;
			}while($n && $n->nodeType != XML_ELEMENT_NODE);
			if($n){
				return new native_simple_html_dom_node ( $n, $this->document );
			}
		}
	}
	
	// get dom node's inner html
	function innertext() {
		$innerHTML = "";
		$children = $this->dom->childNodes;
		if (! $children) {
			return $this->dom->textContent;
		}
		foreach ( $children as $child ) {
			$tmp_dom = new \DOMDocument ();
			$c = $tmp_dom->importNode ( $child, true );
			if(!$c){
				die(var_dump($child));
			}
			$tmp_dom->appendChild ( $c );
			$innerHTML .= trim ( $tmp_dom->saveHTML () );
		}
		return $innerHTML;
	}
	
	// get dom node's outer text (with tag)
	function outertext() {
		$tmp_dom = new \DOMDocument ();
		$tmp_dom->appendChild ( $tmp_dom->importNode ( $this->dom, true ) );
		return $tmp_dom->saveHTML ();
	}
	
	// get dom node's plain text
	function text() {
		return strip_tags ( $this->outertext () );
	}
	
	function xmltext() {
		$ret = $this->innertext ();
		$ret = str_ireplace ( '<![CDATA[', '', $ret );
		$ret = str_replace ( ']]>', '', $ret );
		return $ret;
	}
	
	static function secondPartPush(&$parts,$exp){
		$a = array_reverse($parts);
		if($a[0]{0} == '/'){
			array_push($parts, '*');
		}
		array_push($parts, $exp);
	}
	static function CSStoXpath($rule) {
		$reg ['element'] = "/^([#.]?)([a-z0-9\\*_-]*)((\\|)([a-z0-9\\*_-]*))?/is";
		$reg ['attr1'] = "/^\\[([^\\]]*)\\]/i";
		$reg ['attr2'] = '/^\[\s*([^\*^\^^~=\s]+)\s*((?:\*|\^|~)?=)\s*"?([^"^\]]+)"?\s*\]/is';
		$reg ['attrN'] = "/^:not\\((.*?)\\)/is";
		$reg ['psuedo'] = "/^:([a-z_-])+/is";
		$reg ['gtlt'] = "/^:([g|l])t\\(([0-9])\\)/is";
		$reg ['last'] = "/^:(last |last\\([-]([0-9]+)\\))/is";
		$reg ['first'] = "/^:(first\\([+]([0-9]+)\\)|first)/is";
		$reg ['psuedoN'] = "/^:nth-child\\(([0-9])\\)/is";
		$reg ['combinator'] = "/^(\\s*[>+\\s])?/is";
		$reg ['comma'] = "/^\\s*,/s";
		
		$index = 1;
		$parts = array ("//" );
		$lastRule = NULL;
		
		while ( strlen ( $rule ) > 0 && $rule != $lastRule ) {
			$lastRule = $rule;
			$rule = trim ( $rule );
			if (strlen ( $rule ) > 0) {
				// Match the Element identifier
				$a = preg_match ( $reg ['element'], $rule, $m );
				if ($a) {
					
					if (! isset ( $m [1] )) {
						
						if (isset ( $m [5] )) {
							$parts [$index] = $m [5];
						} else {
							$parts [$index] = $m [2];
						}
					} else if ($m [1] == '#') {
						self::secondPartPush ( $parts, "[@id='" . $m [2] . "']" );
					} else if ($m [1] == '.') {
						self::secondPartPush ( $parts, "[contains(@class, '" . $m [2] . "')]" );
					} else {
						array_push ( $parts, $m [0] );
					}
					$rule = substr ( $rule, strlen ( $m [0] ) );
				}
				
				// Match attribute selectors.
				$a = preg_match ( $reg ['attr2'], $rule, $m );
				if ($a) {
					if ($m [2] == "^=") {
						self::secondPartPush ( $parts, "[starts-with(@" . $m [1] . ", '" . $m [3] . "')]" );
					} elseif ($m [2] == "~=" || $m [2] == "*=") {
						self::secondPartPush ( $parts, "[contains(@" . $m [1] . ", '" . $m [3] . "')]" );
					} else {
						self::secondPartPush ( $parts, "[@" . $m [1] . "='" . $m [3] . "']" );
					}
					$rule = substr ( $rule, strlen ( $m [0] ) );
				} else {
					$a = preg_match ( $reg ['attr1'], $rule, $m );
					if ($a) {
						self::secondPartPush ( $parts, "[@" . $m [1] . "]" );
						$rule = substr ( $rule, strlen ( $m [0] ) );
					}
				}
				
				// register nth-child
				$a = preg_match ( $reg ['psuedoN'], $rule, $m );
				if ($a) {
					self::secondPartPush ( $parts, "[" . $m [1] . "]" );
					$rule = substr ( $rule, strlen ( $m [0] ) );
				}
				
				// gt and lt commands
				$a = preg_match ( $reg ['gtlt'], $rule, $m );
				if ($a) {
					if ($m [1] == "g") {
						$c = ">";
					} else {
						$c = "<";
					}
					
					self::secondPartPush ( $parts, "[position()" . $c . $m [2] . "]" );
					$rule = substr ( $rule, strlen ( $m [0] ) );
				}
				
				// last and last(-n) command
				$a = preg_match ( $reg ['last'], $rule, $m );
				if ($a) {
					if (isset ( $m [2] )) {
						$m [2] = "-" . $m [2];
					}
					self::secondPartPush ( $parts, "[last()" . $m [2] . "]" );
					//print_r($m);
					$rule = substr ( $rule, strlen ( $m [0] ) );
				}
				
				// first and first(+n) command
				$a = preg_match ( $reg ['first'], $rule, $m );
				if ($a) {
					$n = 0;
					if (isset ( $m [2] )) {
						$n = $m [2];
					}
					self::secondPartPush ( $parts, "[$n]" );
					
					$rule = substr ( $rule, strlen ( $m [0] ) );
				
				}
				
				// skip over psuedo classes and psuedo elements
				$a = preg_match ( $reg ['psuedo'], $rule, $m );
				while ( $m ) {
					// loop???
					$rule = substr ( $rule, strlen ( $m [0] ) );
					$a = preg_match ( $reg ['psuedo'], $rule, $m );
				}
				
				// Match combinators
				$a = preg_match ( $reg ['combinator'], $rule, $m );
				if ($a && strlen ( $m [0] ) > 0) {
					if (strpos ( $m [0], ">" )) {
						array_push ( $parts, "/" );
					} else if (strpos ( $m [0], "+" )) {
						array_push ( $parts, "/following-sibling::" );
					} else {
						array_push ( $parts, "//" );
					}
					
					$index = count ( $parts );
					//array_push( $parts, "*" );
					$rule = substr ( $rule, strlen ( $m [0] ) );
				}
				
				$a = preg_match ( $reg ['comma'], $rule, $m );
				if ($a) {
					array_push ( $parts, " | ", "//" );
					$index = count ( $parts ) - 1;
					$rule = substr ( $rule, strlen ( $m [0] ) );
				}
			}
		}
		$xpath = implode ( "", $parts );
		return $xpath;
	}
	
	// find elements by css selector
	function find($selector, $idx = null, $ex = false) {
		$query = '.'.self::CSStoXpath ( $selector );
		
		if($idx == -1){
			echo 'Dom: ',(string)$this,"\r\n";
			echo 'Query: ',$query,"\r\n";
		}
		$xpath = new \DOMXPath ( $this->document );
		$entries = $xpath->query ( $query, $this->dom );
		if($entries === false){
			die(var_dump($selector,libxml_get_last_error(),$query));
		}
		
		$ret = array ();
		if($idx == -1){
			echo 'Length: ',$entries->length,"\r\n";
			$idx = null;
		}
		foreach ( $entries as $k => $v ) {
			if($idx === null){
				$ret [] = new native_simple_html_dom_node ( $v, $this->document );
			}elseif ($k == $idx) {
				return new native_simple_html_dom_node ( $v, $this->document );
			}
		}
		if(!$ret && $ex){
			throw new NodeNotFound ( $selector . ' didnt match any nodes' );
		}
		return $ret;
	}
	
	function __get($name) {
		switch ($name) {
			case 'outertext' :
				return $this->outertext ();
			case 'innertext' :
				return $this->innertext ();
			case 'plaintext' :
				return $this->text ();
			case 'xmltext' :
				return $this->xmltext ();
			default :
				if(method_exists($this->dom,'getAttribute')){
					return $this->dom->getAttribute ( $name );
				}
		}
	}
	
	function __set($name, $value) {
		switch ($name) {
			case 'outertext' :
				if($value){
					$new = new \DOMDocument ();
					$v = $new->loadHTML ( '<h_internal>' . $value . '</h_internal>' );
				
					foreach ( $new->getElementsByTagName ( 'h_internal' )->item ( 0 )->childNodes as $c ) {
						$node = $this->dom->parentNode->insertBefore ( $this->document->importNode ( $c, true ), $this->dom );
					}
				
					if($this->dom){
						$this->dom->parentNode->removeChild ( $this->dom );
					}
					$this->dom = $node;
				}else{
					$this->dom->parentNode->removeChild ( $this->dom );
					$this->dom = null;//
				}
				break;
			case 'innertext' :
				if($this->dom->hasChildNodes()){
					while ( $c = $this->dom->childNodes->item(0) ) {
						$this->dom->removeChild ( $c );
					}
				}
				if($value){
					$new = new \DOMDocument ();
					$v = $new->loadHTML ( '<h_internal>' . $value . '</h_internal>' );
					foreach ( $new->getElementsByTagName ( 'h_internal' )->item ( 0 )->childNodes as $c ) {
						$node = $this->dom->appendChild ( $this->document->importNode ( $c, true ) );
					}
				}
				break;
			default :
				$this->dom->setAttribute ( $name, $value );
		}
	
	}
	
	function __isset($name) {
		switch ($name) {
			case 'outertext' :
				return true;
			case 'innertext' :
				return true;
			case 'plaintext' :
				return true;
		}
		//no value attr: nowrap, checked selected...
		return $this->dom->hasAttribute ( $name );
	}
	
	function __unset($name) {
		return $this->dom->removeAttribute ( $name );
	}
	
	// camel naming conventions
	function getAllAttributes() {
		return $this->attr;
	}
	function getAttribute($name) {
		return $this->__get ( $name );
	}
	function setAttribute($name, $value) {
		$this->__set ( $name, $value );
	}
	function hasAttribute($name) {
		return $this->__isset ( $name );
	}
	function removeAttribute($name) {
		$this->__set ( $name, null );
	}
	function getElementById($id) {
		return $this->find ( "#$id", 0 );
	}
	function getElementsById($id, $idx = null) {
		return $this->find ( "#$id", $idx );
	}
	function getElementByTagName($name) {
		return $this->find ( $name, 0 );
	}
	function getElementsByTagName($name, $idx = null) {
		return $this->find ( $name, $idx );
	}
	function parentNode() {
		return $this->parent ();
	}
	function childNodes($idx = -1) {
		return $this->children ( $idx );
	}
	function firstChild() {
		return $this->first_child ();
	}
	function lastChild() {
		return $this->last_child ();
	}
	function nextSibling() {
		return $this->next_sibling ();
	}
	function previousSibling() {
		return $this->prev_sibling ();
	}
}

// simple html dom parser
// -----------------------------------------------------------------------------
class native_Simple_HTML_DOM {
	private $root;
	private $document;
	
	static function LoadS() {
	
	}
	
	function __construct($str = null) {
		if ($str) {
			if (preg_match ( "/^http:\\/\\//i", $str ) || is_file ( $str ))
				$this->load_file ( $str );
			else
				$this->load ( $str );
		}
	}
	
	function __destruct() {
		$this->clear ();
	}
	
	// load html from string
	function load($str, $lowercase = true) {
		libxml_use_internal_errors ( true );
		
		$doc = new \DOMDocument ();
		$doc->preserveWhiteSpace = false;
		$str = trim($str);
		if($str){
			$doc->loadHTML ( $str );
		}
		
		$this->root = new native_simple_html_dom_node ( $doc, $doc, true );
		$this->document = $doc;
	}
	
	// load html from file
	function load_file() {
		$args = func_get_args ();
		$this->load ( call_user_func_array ( 'file_get_contents', $args ), true );
	}
	
	// save dom as string
	function save($filepath = '') {
		$ret = $this->root->innertext ();
		if ($filepath !== '')
			file_put_contents ( $filepath, $ret );
		return $ret;
	}
	
	// find dom node by css selector
	function find($selector, $idx = null, $ex = false) {
		return $this->root->find ( $selector, $idx, $ex );
	}
	
	// clean up memory due to php5 circular references memory leak...
	function clear() {
		$this->document = $this->root = null;
	}
	
	function __toString() {
		if($this->document)
			return $this->document->saveHTML ();
	}
	
	function __get($name) {
		switch ($name) {
			case 'outertext' :
				return $this->root->innertext ();
			case 'innertext' :
				return $this->root->innertext ();
			case 'plaintext' :
				return $this->root->text ();
		}
	}
	
	// camel naming conventions
	function childNodes($idx = -1) {
		return $this->root->childNodes ( $idx );
	}
	function firstChild() {
		return $this->root->first_child ();
	}
	function lastChild() {
		return $this->root->last_child ();
	}
	function getElementById($id) {
		return $this->find ( "#$id", 0 );
	}
	function getElementsById($id, $idx = null) {
		return $this->find ( "#$id", $idx );
	}
	function getElementByTagName($name) {
		return $this->find ( $name, 0 );
	}
	function getElementsByTagName($name, $idx = -1) {
		return $this->find ( $name, $idx );
	}
	function loadFile() {
		$args = func_get_args ();
		$this->load ( call_user_func_array ( 'file_get_contents', $args ), true );
	}
}

// simple html dom node
// -----------------------------------------------------------------------------
class simple_html_dom_node {
	public $nodetype = Simple_HTML_DOM::_TYPE_TEXT;
	public $tag = 'text';
	public $attr = array ();
	public $children = array ();
	public $nodes = array ();
	public $parent = null;
	public $_ = array ();
	private $dom = null;
	
	function __construct($dom) {
		$this->dom = $dom;
		$dom->nodes [] = $this;
	}
	
	function __destruct() {
		$this->clear ();
	}
	
	function __toString() {
		return $this->outertext ();
	}
	
	// clean up memory due to php5 circular references memory leak...
	function clear() {
		$this->dom = null;
		$this->nodes = null;
		$this->parent = null;
		$this->children = null;
	}
	
	// returns the parent of node
	function parent() {
		return $this->parent;
	}
	
	// returns children of node
	function children($idx = -1) {
		if ($idx === - 1)
			return $this->children;
		if (isset ( $this->children [$idx] ))
			return $this->children [$idx];
		return null;
	}
	
	// returns the first child of node
	function first_child() {
		if (count ( $this->children ) > 0)
			return $this->children [0];
		return null;
	}
	
	// returns the last child of node
	function last_child() {
		if (($count = count ( $this->children )) > 0)
			return $this->children [$count - 1];
		return null;
	}
	
	// returns the next sibling of node    
	function next_sibling() {
		if ($this->parent === null)
			return null;
		$idx = 0;
		$count = count ( $this->parent->children );
		while ( $idx < $count && $this !== $this->parent->children [$idx] )
			++ $idx;
		if (++ $idx >= $count)
			return null;
		return $this->parent->children [$idx];
	}
	
	// returns the previous sibling of node
	function prev_sibling() {
		if ($this->parent === null)
			return null;
		$idx = 0;
		$count = count ( $this->parent->children );
		while ( $idx < $count && $this !== $this->parent->children [$idx] )
			++ $idx;
		if (-- $idx < 0)
			return null;
		return $this->parent->children [$idx];
	}
	
	// get dom node's inner html
	function innertext() {
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_INNER] ))
			return $this->_ [Simple_HTML_DOM::_INFO_INNER];
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] ))
			return $this->dom->restore_noise ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] );
		
		$ret = '';
		foreach ( $this->nodes as $n )
			$ret .= $n->outertext ();
		return $ret;
	}
	
	// get dom node's outer text (with tag)
	function outertext() {
		if ($this->tag === 'root')
			return $this->innertext ();
		
		// trigger callback
		if ($this->dom->callback !== null)
			call_user_func_array ( $this->dom->callback, array ($this ) );
		
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_OUTER] ))
			return $this->_ [Simple_HTML_DOM::_INFO_OUTER];
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] ))
			return $this->dom->restore_noise ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] );
		
		// render begin tag
		$ret = $this->dom->nodes [$this->_ [Simple_HTML_DOM::_INFO_BEGIN]]->makeup ();
		
		// render inner text
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_INNER] ))
			$ret .= $this->_ [Simple_HTML_DOM::_INFO_INNER];
		else {
			foreach ( $this->nodes as $n )
				$ret .= $n->outertext ();
		}
		
		// render end tag
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_END] ) && $this->_ [Simple_HTML_DOM::_INFO_END] != 0)
			$ret .= '</' . $this->tag . '>';
		return $ret;
	}
	
	// get dom node's plain text
	function text() {
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_INNER] ))
			return $this->_ [Simple_HTML_DOM::_INFO_INNER];
		switch ($this->nodetype) {
			case Simple_HTML_DOM::_TYPE_TEXT :
				return $this->dom->restore_noise ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] );
			case Simple_HTML_DOM::_TYPE_COMMENT :
				return '';
			case Simple_HTML_DOM::_TYPE_UNKNOWN :
				return '';
		}
		if (strcasecmp ( $this->tag, 'script' ) === 0)
			return '';
		if (strcasecmp ( $this->tag, 'style' ) === 0)
			return '';
		
		$ret = '';
		foreach ( $this->nodes as $n )
			$ret .= $n->text ();
		return $ret;
	}
	
	function xmltext() {
		$ret = $this->innertext ();
		$ret = str_ireplace ( '<![CDATA[', '', $ret );
		$ret = str_replace ( ']]>', '', $ret );
		return $ret;
	}
	
	// build node's text with tag
	function makeup() {
		// text, comment, unknown
		if (isset ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] ))
			return $this->dom->restore_noise ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] );
		
		$ret = '<' . $this->tag;
		$i = - 1;
		
		foreach ( $this->attr as $key => $val ) {
			++ $i;
			
			// skip removed attribute
			if ($val === null || $val === false)
				continue;
			
			$ret .= $this->_ [Simple_HTML_DOM::_INFO_SPACE] [$i] [0];
			//no value attr: nowrap, checked selected...
			if ($val === true)
				$ret .= $key;
			else {
				switch ($this->_ [Simple_HTML_DOM::_INFO_QUOTE] [$i]) {
					case Simple_HTML_DOM::_QUOTE_DOUBLE :
						$quote = '"';
						break;
					case Simple_HTML_DOM::_QUOTE_SINGLE :
						$quote = '\'';
						break;
					default :
						$quote = '';
				}
				$ret .= $key . $this->_ [Simple_HTML_DOM::_INFO_SPACE] [$i] [1] . '=' . $this->_ [Simple_HTML_DOM::_INFO_SPACE] [$i] [2] . $quote . $val . $quote;
			}
		}
		$ret = $this->dom->restore_noise ( $ret );
		return $ret . $this->_ [Simple_HTML_DOM::_INFO_ENDSPACE] . '>';
	}
	
	// find elements by css selector
	function find($selector, $idx = null, $ex = false) {
		$selectors = $this->parse_selector ( $selector );
		if (($count = count ( $selectors )) === 0)
			return array ();
		$found_keys = array ();
		
		// find each selector
		for($c = 0; $c < $count; ++ $c) {
			if (($levle = count ( $selectors [0] )) === 0)
				return array ();
			if (! isset ( $this->_ [Simple_HTML_DOM::_INFO_BEGIN] ))
				return array ();
			
			$head = array ($this->_ [Simple_HTML_DOM::_INFO_BEGIN] => 1 );
			
			// handle descendant selectors, no recursive!
			for($l = 0; $l < $levle; ++ $l) {
				$ret = array ();
				foreach ( $head as $k => $v ) {
					$n = ($k === - 1) ? $this->dom->root : $this->dom->nodes [$k];
					$n->seek ( $selectors [$c] [$l], $ret );
				}
				$head = $ret;
			}
			
			foreach ( $head as $k => $v ) {
				if (! isset ( $found_keys [$k] ))
					$found_keys [$k] = 1;
			}
		}
		
		// sort keys
		ksort ( $found_keys );
		
		$found = array ();
		foreach ( $found_keys as $k => $v )
			$found [] = $this->dom->nodes [$k];
		
		// return nth-element or array
		if (is_null ( $idx ))
			return $found;
		else if ($idx < 0)
			$idx = count ( $found ) + $idx;
		if (isset ( $found [$idx] ) && is_object ( $found [$idx] )) {
			return $found [$idx];
		}
		if ($ex) {
			throw new NodeNotFound ( $selector . ' didnt match any nodes' );
		}
		return null;
	}
	
	// seek for given conditions
	protected function seek($selector, &$ret) {
		list ( $tag, $key, $val, $exp, $no_key ) = $selector;
		
		// xpath index
		if ($tag && $key && is_numeric ( $key )) {
			$count = 0;
			foreach ( $this->children as $c ) {
				if ($tag === '*' || $tag === $c->tag) {
					if (++ $count == $key) {
						$ret [$c->_ [Simple_HTML_DOM::_INFO_BEGIN]] = 1;
						return;
					}
				}
			}
			return;
		}
		
		$end = (! empty ( $this->_ [Simple_HTML_DOM::_INFO_END] )) ? $this->_ [Simple_HTML_DOM::_INFO_END] : 0;
		if ($end == 0) {
			$parent = $this->parent;
			while ( ! isset ( $parent->_ [Simple_HTML_DOM::_INFO_END] ) && $parent !== null ) {
				$end -= 1;
				$parent = $parent->parent;
			}
			$end += $parent->_ [Simple_HTML_DOM::_INFO_END];
		}
		
		for($i = $this->_ [Simple_HTML_DOM::_INFO_BEGIN] + 1; $i < $end; ++ $i) {
			$node = $this->dom->nodes [$i];
			$pass = true;
			
			if ($tag === '*' && ! $key) {
				if (in_array ( $node, $this->children, true ))
					$ret [$i] = 1;
				continue;
			}
			
			// compare tag
			if ($tag && $tag != $node->tag && $tag !== '*') {
				$pass = false;
			}
			// compare key
			if ($pass && $key) {
				if ($no_key) {
					if (isset ( $node->attr [$key] ))
						$pass = false;
				} else if (! isset ( $node->attr [$key] ))
					$pass = false;
			}
			// compare value
			if ($pass && $key && $val && $val !== '*') {
				$check = $this->match ( $exp, $val, $node->attr [$key] );
				// handle multiple class
				if (! $check && strcasecmp ( $key, 'class' ) === 0) {
					foreach ( explode ( ' ', $node->attr [$key] ) as $k ) {
						$check = $this->match ( $exp, $val, $k );
						if ($check)
							break;
					}
				}
				if (! $check)
					$pass = false;
			}
			if ($pass)
				$ret [$i] = 1;
			unset ( $node );
		}
	}
	
	protected function match($exp, $pattern, $value) {
		switch ($exp) {
			case '=' :
				return ($value === $pattern);
			case '!=' :
				return ($value !== $pattern);
			case '^=' :
				return preg_match ( "/^" . preg_quote ( $pattern, '/' ) . "/", $value );
			case '$=' :
				return preg_match ( "/" . preg_quote ( $pattern, '/' ) . "$/", $value );
			case '*=' :
				if ($pattern [0] == '/')
					return preg_match ( $pattern, $value );
				return preg_match ( "/" . $pattern . "/i", $value );
		}
		return false;
	}
	
	protected function parse_selector($selector_string) {
		// pattern of CSS selectors, modified from mootools
		$pattern = "/([\\w-:\\*]*)(?:\\#([\\w-]+)|\\.([\\w-]+))?(?:\\[@?(!?[\\w-]+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\\])?([\\/, ]+)/is";
		preg_match_all ( $pattern, trim ( $selector_string ) . ' ', $matches, PREG_SET_ORDER );
		$selectors = array ();
		$result = array ();
		//print_r($matches);
		

		foreach ( $matches as $m ) {
			$m [0] = trim ( $m [0] );
			if ($m [0] === '' || $m [0] === '/' || $m [0] === '//')
				continue;
			
		// for borwser grnreated xpath
			if ($m [1] === 'tbody')
				continue;
			
			list ( $tag, $key, $val, $exp, $no_key ) = array ($m [1], null, null, '=', false );
			if (! empty ( $m [2] )) {
				$key = 'id';
				$val = $m [2];
			}
			if (! empty ( $m [3] )) {
				$key = 'class';
				$val = $m [3];
			}
			if (! empty ( $m [4] )) {
				$key = $m [4];
			}
			if (! empty ( $m [5] )) {
				$exp = $m [5];
			}
			if (! empty ( $m [6] )) {
				$val = $m [6];
			}
			
			// convert to lowercase
			if ($this->dom->lowercase) {
				$tag = strtolower ( $tag );
				$key = strtolower ( $key );
			}
			//elements that do NOT have the specified attribute
			if (isset ( $key [0] ) && $key [0] === '!') {
				$key = substr ( $key, 1 );
				$no_key = true;
			}
			
			$result [] = array ($tag, $key, $val, $exp, $no_key );
			if (trim ( $m [7] ) === ',') {
				$selectors [] = $result;
				$result = array ();
			}
		}
		if (count ( $result ) > 0)
			$selectors [] = $result;
		return $selectors;
	}
	
	function __get($name) {
		if (isset ( $this->attr [$name] ))
			return $this->attr [$name];
		switch ($name) {
			case 'outertext' :
				return $this->outertext ();
			case 'innertext' :
				return $this->innertext ();
			case 'plaintext' :
				return $this->text ();
			case 'xmltext' :
				return $this->xmltext ();
			default :
				return array_key_exists ( $name, $this->attr );
		}
	}
	
	function __set($name, $value) {
		switch ($name) {
			case 'outertext' :
				return $this->_ [Simple_HTML_DOM::_INFO_OUTER] = $value;
			case 'innertext' :
				if (isset ( $this->_ [Simple_HTML_DOM::_INFO_TEXT] ))
					return $this->_ [Simple_HTML_DOM::_INFO_TEXT] = $value;
				return $this->_ [Simple_HTML_DOM::_INFO_INNER] = $value;
		}
		if (! isset ( $this->attr [$name] )) {
			$this->_ [Simple_HTML_DOM::_INFO_SPACE] [] = array (' ', '', '' );
			$this->_ [Simple_HTML_DOM::_INFO_QUOTE] [] = Simple_HTML_DOM::_QUOTE_DOUBLE;
		}
		$this->attr [$name] = $value;
	}
	
	function __isset($name) {
		switch ($name) {
			case 'outertext' :
				return true;
			case 'innertext' :
				return true;
			case 'plaintext' :
				return true;
		}
		//no value attr: nowrap, checked selected...
		return (array_key_exists ( $name, $this->attr )) ? true : isset ( $this->attr [$name] );
	}
	
	function __unset($name) {
		if (isset ( $this->attr [$name] ))
			unset ( $this->attr [$name] );
	}
	
	// camel naming conventions
	function getAllAttributes() {
		return $this->attr;
	}
	function getAttribute($name) {
		return $this->__get ( $name );
	}
	function setAttribute($name, $value) {
		$this->__set ( $name, $value );
	}
	function hasAttribute($name) {
		return $this->__isset ( $name );
	}
	function removeAttribute($name) {
		$this->__set ( $name, null );
	}
	function getElementById($id) {
		return $this->find ( "#$id", 0 );
	}
	function getElementsById($id, $idx = null) {
		return $this->find ( "#$id", $idx );
	}
	function getElementByTagName($name) {
		return $this->find ( $name, 0 );
	}
	function getElementsByTagName($name, $idx = null) {
		return $this->find ( $name, $idx );
	}
	function parentNode() {
		return $this->parent ();
	}
	function childNodes($idx = -1) {
		return $this->children ( $idx );
	}
	function firstChild() {
		return $this->first_child ();
	}
	function lastChild() {
		return $this->last_child ();
	}
	function nextSibling() {
		return $this->next_sibling ();
	}
	function previousSibling() {
		return $this->prev_sibling ();
	}
}

// simple html dom parser
// -----------------------------------------------------------------------------
class Simple_HTML_DOM {
	const _TYPE_ELEMENT = 1;
	const _TYPE_COMMENT = 2;
	const _TYPE_TEXT = 3;
	const _TYPE_ENDTAG = 4;
	const _TYPE_ROOT = 5;
	const _TYPE_UNKNOWN = 6;
	const _QUOTE_DOUBLE = 0;
	const _QUOTE_SINGLE = 1;
	const _QUOTE_NO = 3;
	const _INFO_BEGIN = 0;
	const _INFO_END = 1;
	const _INFO_QUOTE = 2;
	const _INFO_SPACE = 3;
	const _INFO_TEXT = 4;
	const _INFO_INNER = 5;
	const _INFO_OUTER = 6;
	const _INFO_ENDSPACE = 7;
	
	public $root = null;
	public $nodes = array ();
	public $callback = null;
	public $lowercase = false;
	protected $pos;
	protected $doc;
	protected $char;
	protected $size;
	protected $cursor;
	protected $parent;
	protected $noise = array ();
	protected $token_blank = " \t\r\n";
	protected $token_equal = ' =/>';
	protected $token_slash = " />\r\n\t";
	protected $token_attr = ' >';
	// use isset instead of in_array, performance boost about 30%...
	protected $self_closing_tags = array ('img' => 1, 'br' => 1, 'input' => 1, 'meta' => 1, 'link' => 1, 'hr' => 1, 'base' => 1, 'embed' => 1, 'spacer' => 1 );
	protected $block_tags = array ('root' => 1, 'body' => 1, 'form' => 1, 'div' => 1, 'span' => 1, 'table' => 1 );
	protected $optional_closing_tags = array ('tr' => array ('tr' => 1, 'td' => 1, 'th' => 1 ), 'th' => array ('th' => 1 ), 'td' => array ('td' => 1 ), 'li' => array ('li' => 1 ), 'dt' => array ('dt' => 1, 'dd' => 1 ), 'dd' => array ('dd' => 1, 'dt' => 1 ), 'dl' => array ('dd' => 1, 'dt' => 1 ), 'p' => array ('p' => 1 ), 'nobr' => array ('nobr' => 1 ) );
	
	static function LoadS() {
	
	}
	
	function __construct($str = null) {
		if ($str) {
			if (preg_match ( "/^http:\\/\\//i", $str ) || is_file ( $str ))
				$this->load_file ( $str );
			else
				$this->load ( $str );
		}
	}
	
	function __destruct() {
		$this->clear ();
	}
	
	// load html from string
	function load($str, $lowercase = true) {
		// prepare
		$this->prepare ( $str, $lowercase );
		// strip out comments
		$this->remove_noise ( "'<!--(.*?)-->'is" );
		// strip out cdata
		$this->remove_noise ( "'<!\\[CDATA\\[(.*?)\\]\\]>'is", true );
		// strip out <style> tags
		$this->remove_noise ( "'<\\s*style[^>]*[^/]>(.*?)<\\s*/\\s*style\\s*>'is" );
		$this->remove_noise ( "'<\\s*style\\s*>(.*?)<\\s*/\\s*style\\s*>'is" );
		// strip out <script> tags
		$this->remove_noise ( "'<\\s*script[^>]*[^/]>(.*?)<\\s*/\\s*script\\s*>'is" );
		$this->remove_noise ( "'<\\s*script\\s*>(.*?)<\\s*/\\s*script\\s*>'is" );
		// strip out preformatted tags
		$this->remove_noise ( "'<\\s*(?:code)[^>]*>(.*?)<\\s*/\\s*(?:code)\\s*>'is" );
		// strip out server side scripts
		$this->remove_noise ( "'(<\\?)(.*?)(\\?>)'s", true );
		// strip smarty scripts
		$this->remove_noise ( "'(\\{\\w)(.*?)(\\})'s", true );
		
		// parsing
		while ( $this->parse () )
			;
		
		// end
		$this->root->_ [self::_INFO_END] = $this->cursor;
	}
	
	// load html from file
	function load_file() {
		$args = func_get_args ();
		$this->load ( call_user_func_array ( 'file_get_contents', $args ), true );
	}
	
	// set callback function
	function set_callback($function_name) {
		$this->callback = $function_name;
	}
	
	// remove callback function
	function remove_callback() {
		$this->callback = null;
	}
	
	// save dom as string
	function save($filepath = '') {
		$ret = $this->root->innertext ();
		if ($filepath !== '')
			file_put_contents ( $filepath, $ret );
		return $ret;
	}
	
	// find dom node by css selector
	function find($selector, $idx = null, $ex = false) {
		return $this->root->find ( $selector, $idx, $ex );
	}
	
	// clean up memory due to php5 circular references memory leak...
	function clear() {
		foreach ( $this->nodes as $n ) {
			$n->clear ();
			$n = null;
		}
		if (isset ( $this->parent )) {
			$this->parent->clear ();
			unset ( $this->parent );
		}
		if (isset ( $this->root )) {
			$this->root->clear ();
			unset ( $this->root );
		}
		unset ( $this->doc );
		unset ( $this->noise );
	}
	
	function dump($show_attr = true) {
		$this->root->dump ( $show_attr );
	}
	
	// prepare HTML data and init everything
	protected function prepare($str, $lowercase = true) {
		$this->clear ();
		$this->doc = $str;
		$this->pos = 0;
		$this->cursor = 1;
		$this->noise = array ();
		$this->nodes = array ();
		$this->lowercase = $lowercase;
		$this->root = new simple_html_dom_node ( $this );
		$this->root->tag = 'root';
		$this->root->_ [self::_INFO_BEGIN] = - 1;
		$this->root->nodetype = self::_TYPE_ROOT;
		$this->parent = $this->root;
		// set the length of content
		$this->size = strlen ( $str );
		if ($this->size > 0)
			$this->char = $this->doc [0];
	}
	
	// parse html content
	protected function parse() {
		if (($s = $this->copy_until_char ( '<' )) === '')
			return $this->read_tag ();
		
		// text
		$node = new simple_html_dom_node ( $this );
		++ $this->cursor;
		$node->_ [self::_INFO_TEXT] = $s;
		$this->link_nodes ( $node, false );
		return true;
	}
	
	// read tag info
	protected function read_tag() {
		if ($this->char !== '<') {
			$this->root->_ [self::_INFO_END] = $this->cursor;
			return false;
		}
		$begin_tag_pos = $this->pos;
		$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
		

		// end tag
		if ($this->char === '/') {
			$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
			$this->skip ( $this->token_blank_t );
			$tag = $this->copy_until_char ( '>' );
			
			// skip attributes in end tag
			if (($pos = strpos ( $tag, ' ' )) !== false)
				$tag = substr ( $tag, 0, $pos );
			
			$parent_lower = strtolower ( $this->parent->tag );
			$tag_lower = strtolower ( $tag );
			
			if ($parent_lower !== $tag_lower) {
				if (isset ( $this->optional_closing_tags [$parent_lower] ) && isset ( $this->block_tags [$tag_lower] )) {
					$this->parent->_ [self::_INFO_END] = 0;
					$org_parent = $this->parent;
					
					while ( ($this->parent->parent) && strtolower ( $this->parent->tag ) !== $tag_lower )
						$this->parent = $this->parent->parent;
					
					if (strtolower ( $this->parent->tag ) !== $tag_lower) {
						$this->parent = $org_parent; // restore origonal parent
						if ($this->parent->parent)
							$this->parent = $this->parent->parent;
						$this->parent->_ [self::_INFO_END] = $this->cursor;
						return $this->as_text_node ( $tag );
					}
				} else if (($this->parent->parent) && isset ( $this->block_tags [$tag_lower] )) {
					$this->parent->_ [self::_INFO_END] = 0;
					$org_parent = $this->parent;
					
					while ( ($this->parent->parent) && strtolower ( $this->parent->tag ) !== $tag_lower )
						$this->parent = $this->parent->parent;
					
					if (strtolower ( $this->parent->tag ) !== $tag_lower) {
						$this->parent = $org_parent; // restore origonal parent
						$this->parent->_ [self::_INFO_END] = $this->cursor;
						return $this->as_text_node ( $tag );
					}
				} else if (($this->parent->parent) && strtolower ( $this->parent->parent->tag ) === $tag_lower) {
					$this->parent->_ [self::_INFO_END] = 0;
					$this->parent = $this->parent->parent;
				} else
					return $this->as_text_node ( $tag );
			}
			
			$this->parent->_ [self::_INFO_END] = $this->cursor;
			if ($this->parent->parent)
				$this->parent = $this->parent->parent;
			
			$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
			return true;
		}
		
		$node = new simple_html_dom_node ( $this );
		$node->_ [self::_INFO_BEGIN] = $this->cursor;
		++ $this->cursor;
		$tag = $this->copy_until ( $this->token_slash );
		
		// doctype, cdata & comments...
		if (isset ( $tag [0] ) && $tag [0] === '!') {
			$node->_ [self::_INFO_TEXT] = '<' . $tag . $this->copy_until_char ( '>' );
			
			if (isset ( $tag [2] ) && $tag [1] === '-' && $tag [2] === '-') {
				$node->nodetype = self::_TYPE_COMMENT;
				$node->tag = 'comment';
			} else {
				$node->nodetype = self::_TYPE_UNKNOWN;
				$node->tag = 'unknown';
			}
			
			if ($this->char === '>')
				$node->_ [self::_INFO_TEXT] .= '>';
			$this->link_nodes ( $node, true );
			$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
			return true;
		}
		
		// text
		if ($pos = strpos ( $tag, '<' ) !== false) {
			$tag = '<' . substr ( $tag, 0, - 1 );
			$node->_ [self::_INFO_TEXT] = $tag;
			$this->link_nodes ( $node, false );
			$this->char = $this->doc [-- $this->pos]; // prev
			return true;
		}
		
		if (! preg_match ( "/^[\\w-:]+$/", $tag )) {
			$node->_ [self::_INFO_TEXT] = '<' . $tag . $this->copy_until ( '<>' );
			if ($this->char === '<') {
				$this->link_nodes ( $node, false );
				return true;
			}
			
			if ($this->char === '>')
				$node->_ [self::_INFO_TEXT] .= '>';
			$this->link_nodes ( $node, false );
			$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
			return true;
		}
		
		// begin tag
		$node->nodetype = self::_TYPE_ELEMENT;
		$tag_lower = strtolower ( $tag );
		$node->tag = ($this->lowercase) ? $tag_lower : $tag;
		
		// handle optional closing tags
		if (isset ( $this->optional_closing_tags [$tag_lower] )) {
			while ( isset ( $this->optional_closing_tags [$tag_lower] [strtolower ( $this->parent->tag )] ) ) {
				$this->parent->_ [self::_INFO_END] = 0;
				$this->parent = $this->parent->parent;
			}
			$node->parent = $this->parent;
		}
		
		$guard = 0; // prevent infinity loop
		$space = array ($this->copy_skip ( $this->token_blank ), '', '' );
		
		// attributes
		do {
			if ($this->char !== null && $space [0] === '')
				break;
			$name = $this->copy_until ( $this->token_equal );
			if ($guard === $this->pos) {
				$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
				continue;
			}
			$guard = $this->pos;
			
			// handle endless '<'
			if ($this->pos >= $this->size - 1 && $this->char !== '>') {
				$node->nodetype = self::_TYPE_TEXT;
				$node->_ [self::_INFO_END] = 0;
				$node->_ [self::_INFO_TEXT] = '<' . $tag . $space [0] . $name;
				$node->tag = 'text';
				$this->link_nodes ( $node, false );
				return true;
			}
			
			// handle mismatch '<'
			if ($this->doc [$this->pos - 1] == '<') {
				$node->nodetype = self::_TYPE_TEXT;
				$node->tag = 'text';
				$node->attr = array ();
				$node->_ [self::_INFO_END] = 0;
				$node->_ [self::_INFO_TEXT] = substr ( $this->doc, $begin_tag_pos, $this->pos - $begin_tag_pos - 1 );
				$this->pos -= 2;
				$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
				$this->link_nodes ( $node, false );
				return true;
			}
			
			if ($name !== '/' && $name !== '') {
				$space [1] = $this->copy_skip ( $this->token_blank );
				$name = $this->restore_noise ( $name );
				if ($this->lowercase)
					$name = strtolower ( $name );
				if ($this->char === '=') {
					$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
					$this->parse_attr ( $node, $name, $space );
				} else {
					//no value attr: nowrap, checked selected...
					$node->_ [self::_INFO_QUOTE] [] = self::_QUOTE_NO;
					$node->attr [$name] = true;
					if ($this->char != '>')
						$this->char = $this->doc [-- $this->pos]; // prev
				}
				$node->_ [self::_INFO_SPACE] [] = $space;
				$space = array ($this->copy_skip ( $this->token_blank ), '', '' );
			} else
				break;
		} while ( $this->char !== '>' && $this->char !== '/' );
		
		$this->link_nodes ( $node, true );
		$node->_ [self::_INFO_ENDSPACE] = $space [0];
		
		// check self closing
		if ($this->copy_until_char_escape ( '>' ) === '/') {
			$node->_ [self::_INFO_ENDSPACE] .= '/';
			$node->_ [self::_INFO_END] = 0;
		} else {
			// reset parent
			if (! isset ( $this->self_closing_tags [strtolower ( $node->tag )] ))
				$this->parent = $node;
		}
		$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
		return true;
	}
	
	// parse attributes
	protected function parse_attr($node, $name, &$space) {
		$space [2] = $this->copy_skip ( $this->token_blank );
		switch ($this->char) {
			case '"' :
				$node->_ [self::_INFO_QUOTE] [] = self::_QUOTE_DOUBLE;
				$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
				$node->attr [$name] = $this->restore_noise ( $this->copy_until_char_escape ( '"' ) );
				$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
				break;
			case '\'' :
				$node->_ [self::_INFO_QUOTE] [] = self::_QUOTE_SINGLE;
				$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
				$node->attr [$name] = $this->restore_noise ( $this->copy_until_char_escape ( '\'' ) );
				$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
				break;
			default :
				$node->_ [self::_INFO_QUOTE] [] = self::_QUOTE_NO;
				$node->attr [$name] = $this->restore_noise ( $this->copy_until ( $this->token_attr ) );
		}
	}
	
	// link node's parent
	protected function link_nodes(&$node, $is_child) {
		$node->parent = $this->parent;
		$this->parent->nodes [] = $node;
		if ($is_child)
			$this->parent->children [] = $node;
	}
	
	// as a text node
	protected function as_text_node($tag) {
		$node = new simple_html_dom_node ( $this );
		++ $this->cursor;
		$node->_ [self::_INFO_TEXT] = '</' . $tag . '>';
		$this->link_nodes ( $node, false );
		$this->char = (++ $this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
		return true;
	}
	
	protected function skip($chars) {
		$this->pos += strspn ( $this->doc, $chars, $this->pos );
		$this->char = ($this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
	}
	
	protected function copy_skip($chars) {
		$pos = $this->pos;
		$len = strspn ( $this->doc, $chars, $pos );
		$this->pos += $len;
		$this->char = ($this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
		if ($len === 0)
			return '';
		return substr ( $this->doc, $pos, $len );
	}
	
	protected function copy_until($chars) {
		$pos = $this->pos;
		$len = strcspn ( $this->doc, $chars, $pos );
		$this->pos += $len;
		$this->char = ($this->pos < $this->size) ? $this->doc [$this->pos] : null; // next
		return substr ( $this->doc, $pos, $len );
	}
	
	protected function copy_until_char($char) {
		if ($this->char === null)
			return '';
		
		if (($pos = strpos ( $this->doc, $char, $this->pos )) === false) {
			$ret = substr ( $this->doc, $this->pos, $this->size - $this->pos );
			$this->char = null;
			$this->pos = $this->size;
			return $ret;
		}
		
		if ($pos === $this->pos)
			return '';
		$pos_old = $this->pos;
		$this->char = $this->doc [$pos];
		$this->pos = $pos;
		return substr ( $this->doc, $pos_old, $pos - $pos_old );
	}
	
	protected function copy_until_char_escape($char) {
		if ($this->char === null)
			return '';
		
		$start = $this->pos;
		while ( 1 ) {
			if (($pos = strpos ( $this->doc, $char, $start )) === false) {
				$ret = substr ( $this->doc, $this->pos, $this->size - $this->pos );
				$this->char = null;
				$this->pos = $this->size;
				return $ret;
			}
			
			if ($pos === $this->pos)
				return '';
			
			if ($this->doc [$pos - 1] === '\\') {
				$start = $pos + 1;
				continue;
			}
			
			$pos_old = $this->pos;
			$this->char = $this->doc [$pos];
			$this->pos = $pos;
			return substr ( $this->doc, $pos_old, $pos - $pos_old );
		}
	}
	
	// remove noise from html content
	protected function remove_noise($pattern, $remove_tag = false) {
		$count = preg_match_all ( $pattern, $this->doc, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
		
		for($i = $count - 1; $i > - 1; -- $i) {
			$key = '___noise___' . sprintf ( '% 3d', count ( $this->noise ) + 100 );
			$idx = ($remove_tag) ? 0 : 1;
			$this->noise [$key] = $matches [$i] [$idx] [0];
			$this->doc = substr_replace ( $this->doc, $key, $matches [$i] [$idx] [1], strlen ( $matches [$i] [$idx] [0] ) );
		}
		
		// reset the length of content
		$this->size = strlen ( $this->doc );
		if ($this->size > 0)
			$this->char = $this->doc [0];
	}
	
	// restore noise to html content
	function restore_noise($text) {
		while ( ($pos = strpos ( $text, '___noise___' )) !== false ) {
			$key = '___noise___' . $text [$pos + 11] . $text [$pos + 12] . $text [$pos + 13];
			if (isset ( $this->noise [$key] ))
				$text = substr ( $text, 0, $pos ) . $this->noise [$key] . substr ( $text, $pos + 14 );
		}
		return $text;
	}
	
	function __toString() {
		return $this->root->innertext ();
	}
	
	function __get($name) {
		switch ($name) {
			case 'outertext' :
				return $this->root->innertext ();
			case 'innertext' :
				return $this->root->innertext ();
			case 'plaintext' :
				return $this->root->text ();
		}
	}
	
	// camel naming conventions
	function childNodes($idx = -1) {
		return $this->root->childNodes ( $idx );
	}
	function firstChild() {
		return $this->root->first_child ();
	}
	function lastChild() {
		return $this->root->last_child ();
	}
	function getElementById($id) {
		return $this->find ( "#$id", 0 );
	}
	function getElementsById($id, $idx = null) {
		return $this->find ( "#$id", $idx );
	}
	function getElementByTagName($name) {
		return $this->find ( $name, 0 );
	}
	function getElementsByTagName($name, $idx = -1) {
		return $this->find ( $name, $idx );
	}
	function loadFile() {
		$args = func_get_args ();
		$this->load ( call_user_func_array ( 'file_get_contents', $args ), true );
	}
}
?>