<?php
namespace Web\Templates;

use Web\Widget;

use Web\PageHandler\IPage;

use Web\PageHandler\SubRequest;

use HTML\Form\Builder\FormBuilder;

class Scope {
	public $form;
	public $vars = array();
	public $handler;
	
	function __construct(array $vars,$handler){
		$this->form = new FormBuilder();
		$this->vars = $vars;
		$this->handler = $handler;
	}
	
	function bind($var){
		foreach($var as $k=>$v){
			$this->$k = $v;
		}
	}
	
	/* Helper Functions */
	function html($string,$encoding='UTF-8'){
		return htmlspecialchars(@iconv ( $encoding, $encoding."//IGNORE", $string ));
	}
	function h($string){
		return $this->html($string);
	}
	
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
			return \Server::getSiteRoot().$first_part;
		}
		
		return $object;
	}
	function u($object){
		return $this->url($object);
	}
	
	function incl($name,$container = 'HTML'){
		$_ = $this;
		$___path = \Web\Template::getPath($name,$container);
		if(!$___path){
			throw new \Exception('Couldnt find '.$name.' from '.$container.' to include.');
		}
		include($___path);
	}
	
	function subrequest(IPage $page){
		$sub = new SubRequest($page);
		$sub = $sub->Execute('GET');
		return $sub;
	}
	function sub(IPage $page){
		return $this->subrequest($page);
	}
	
	function widget($name,$variables = array()){
		return Widget::Load($name, $variables);
	}
	
	function odd($number,$echo){
		if(($number % 2) == 1) return $echo;
	}
	function even($number,$echo){
		if(($number % 2) == 0) return $echo;
	}
}