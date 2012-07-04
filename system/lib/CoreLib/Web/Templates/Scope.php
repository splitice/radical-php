<?php
namespace Web\Templates;

use Web\Widget;
use Web\Page\Handler\IPage;
use Web\Page\Handler\SubRequest;
use Web\Form\Builder\FormBuilder;

/**
 * The scope passed to templates, commonly referred to as ($_)
 * as thats how the default PHPTemplate system exports it.
 * 
 * @author SplitIce
 *
 */
class Scope {
	public $form;
	public $vars = array();
	public $handler;
	
	/**
	 * Create a scope to be passed to a template (or template adapter)
	 * 
	 * @param array $vars the variables to put in the scope
	 * @param IPage $handler the page handler that called for a template
	 */
	function __construct(array $vars,IPage $handler = null){
		$this->form = new FormBuilder();
		$this->vars = $vars;
		$this->handler = $handler;
	}
	
	/**
	 * Bind variables to this scope.
	 * 
	 * ```
	 * $_->bind(array('message','hello'));
	 * echo $_->message;//Outputs "hello"
	 * ```
	 * 
	 * @param array $var
	 */
	function bind(array $var){
		foreach($var as $k=>$v){
			$this->$k = $v;
		}
	}
	
	/* Helper Functions */
	/**
	 * Encode a $string in html encoding corrosponding to $encoding charset.
	 * 
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 */
	function html($string,$encoding='UTF-8'){
		return htmlspecialchars(@iconv ( $encoding, $encoding."//IGNORE", $string ));
	}
	/**
	 * Shorthand for html
	 * 
	 * @see Scope::html
	 * @param string $string
	 * @return string
	 */
	function h($string){
		return $this->html($string);
	}
	
	/**
	 * Convert an object or string reference to a URL that will work.
	 * 
	 * @param mixed $object
	 * @throws \Exception
	 * @return string
	 */
	function url($object){
		if(is_object($object)){
			$object = $object->toURL();
		}
		if(!is_string($object)){
			throw new \Exception('Unknown type to URLify: '.gettype($object));
		}
		
		//Check for a full 'schemed' URL
		$first_part = substr($object,0,6);
		if($first_part == 'http:/' || $first_part == 'ftp://' || $first_part == 'https:'){
			return $object;//URL is full
		}
		
		//Is relative? Make siterooted
		if($first_part{0} != '/'){
			$object = \Core\Server::getSiteRoot().$object;
		}
		
		return $object;
	}
	/**
	 * Shorthand for url
	 * 
	 * @see Scope::url
	 * @param mixed $object
	 * @return string
	 */
	function u($object){
		return $this->url($object);
	}
	
	/**
	 * Include a file in this template
	 * 
	 * @param string $name
	 * @param string $container
	 * @throws \Exception
	 */
	function incl($name,$container = 'HTML'){
		$_ = $this;
		$___path = \Web\Template::getPath($name,$container);
		if(!$___path){
			throw new \Exception('Couldnt find '.$name.' from '.$container.' to include.');
		}
		include($___path);
	}
	
	/**
	 * Shorthand for incl
	 * 
	 * @see Scope::incl
	 * @param string $name
	 * @param string $container
	 */
	function inc($name,$container = 'HTML'){
		return $this->incl($name,$container);
	}
	
	/**
	 * Perform a sub request
	 * 
	 * @param IPage $page Page Handler to execute
	 * @return string the result
	 */
	function subrequest(IPage $page){
		$sub = new SubRequest($page);
		$sub = $sub->Execute('GET');
		return $sub;
	}
	
	/**
	 * Short hand for subrequest
	 * 
	 * @see Scopre::subrequest
	 * @param IPage $page
	 * @return string
	 */
	function sub(IPage $page){
		return $this->subrequest($page);
	}
	
	/**
	 * Include a widget
	 * 
	 * @param string $name
	 * @param array $variables
	 */
	function widget($name,$variables = array()){
		return Widget::Load($name, $variables);
	}
	
	/**
	 * Return $echo if $number is odd
	 * 
	 * @param int $number
	 * @param string $echo
	 * @return string
	 */
	function odd($number,$echo){
		if(($number % 2) == 1) return $echo;
	}
	
	/**
	 * Return $echo if $number is even
	 *
	 * @param int $number
	 * @param string $echo
	 * @return string
	 */
	function even($number,$echo){
		if(($number % 2) == 0) return $echo;
	}
}