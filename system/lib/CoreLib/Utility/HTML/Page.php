<?php
namespace HTML;

class Page {
	protected $dom;
	public $data;
	public $url;
	static $_absoluteize = array(
		'a'=>'href',
		'script'=>'src',
		'link'=>'href',
		'img'=>'src'	
	);
	
	function __construct($data,$url){
		$this->data = $data;
		$this->url = $url;
		Simple_HTML_DOM::LoadS();
		$this->dom = str_get_dom($data);
	}
	
	function absoluteize(){
		foreach(static::$_absoluteize as $tag=>$attribute){
			foreach($this->dom->find($tag.'['.$attribute.']') as $link){
				$link->$attribute = \Net\URL\Helpers::url_to_absolute($this->url, $link->$attribute);
			}
		}
	}
	
	/**
	 * @return the $dom
	 */
	public function getDom() {
		return $this->dom;
	}

	function __destruct(){
		$this->dom->clear();
		$this->dom = null;
	}
}