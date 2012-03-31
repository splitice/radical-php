<?php
namespace Web\Pages;

use Web\PageHandler\PageBase;

class API extends PageBase {
	protected $object;
	protected $method;
	protected $type;
	protected $error;
	
	function __construct($data){
		$this->type = $data['type'];
		if(isset($data['error'])){
			$this->error = $data['error'];
		}else{
			$this->object = $data['object'];
			$this->method = $data['method'];
		}
	}
	function _addChild($k,$v,$xml){
		$xml->addChild($v,$k);
	}
	function toXML(array $array){
		$xml = new \SimpleXMLElement('<API/>');
		array_walk_recursive($array, array ($this, '_addChild'), $xml);
		return $xml->asXML();
	}
	function GET(){
		$ret = array();
		
		if($this->error){
			$ret['error'] = $this->error;
		}else{
			$m = $this->method;
			
			try {
				$ret['response'] = $this->object->$m();
			}catch(\Exception $ex){
				if(ob_get_level()) ob_clean();
				$ret['error'] = $ex->getMessage();
			}
		}
		
		$headers = \Web\PageHandler::$stack->top()->headers;
		
		switch($this->type){
			case 'json':
				if(isset($_GET['callback'])){
					echo $_GET['callback'],'(';
				}
				echo \Basic\JSON::encode($ret);
				if(isset($_GET['callback'])){
					echo ');';
					$headers->Add('Content-Type','text/javascript');
				}else{
					$headers->Add('Content-Type','text/json');
				}
				break;
				
			case 'xml':
				echo $this->toXML($ret);
				$headers->Add('Content-Type','text/xml');
				break;
				
			case 'ps':
				echo serialize($ret);
				$headers->Add('Content-Type','text/plain');
				break;
				
			default:
				if(isset($ret['response'])){
					echo $ret['response'];
				}
		}
	}
}