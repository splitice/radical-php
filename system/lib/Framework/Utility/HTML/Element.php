<?php
namespace Utility\HTML;

class Element extends SingleTag {
	public $inner;
	protected $singleClose = true;
	public $writeEndTag = true;
	
	function __construct($tag,$attributes = array(),$inner = null){
		parent::__construct($tag,$attributes);
		$this->inner = $inner;
	}
	
	private $html;
	function html_override($html){
		$this->html = $html;
	}
	
	function __toString(){
		if($this->html !== null){
			return $this->html;
		}
		
		$ret = parent::__toString();
		if(!$this->inner && $this->singleClose){
			$ret = substr($ret,0,-1).'/>';
		}
		
		if($this->inner){
			if(is_array($this->inner)){
				foreach($this->inner as $v){
					$ret .= $v;
				}
			}else{
				$ret .= $this->inner;
			}
			if($this->writeEndTag && $this->singleClose)
				$ret .= '</'.$this->tag.'>';
		}
		
		if($this->writeEndTag && !$this->singleClose)
			$ret .= '</'.$this->tag.'>';
		return $ret;
	}
}