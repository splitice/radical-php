<?php
namespace DDL\Hosts\Check\Internal;

abstract class HostBase implements \DDL\Hosts\Check\Interfaces\IDDLHostCheck {
	const HOST_SCORE = null;
	const HOST_ABBR = null;
	const HOST_DOMAIN = null;
	const HOST_REGEX = null;
	const IMAGE_ORDER = null;
	
	function Recognise($data){
		$links = array();
		
		//Parse HTML
		\HTML\Simple_HTML_DOM::LoadS();
		$b = \HTML\str_get_dom($data);
		foreach($b->find('a') as $link){
			if($linka = self::RecogniseSingle($link->href)){
				$links[] = $linka;
				$link->innerhtml = '';//Quite often the text contains ...'s which are incorrect urls
			}
		}
		$b->clear();
		
		//Get text links
		if($links_all = $this->RecogniseAll($data)){
			$links = array_merge($links,$links_all);
		}
		
		//Uniqueify -- requires the creation of an object incase of multiple->single links
		$links = array_unique($links);//Stage 1 simple test
		
		//Return
		return $links;
	}
	
	function RecogniseSingle($link){
		if(static::HOST_REGEX === null){
			return false;
		}
		$m = array();
		if(preg_match('#'.static::HOST_REGEX.'#i',$link,$m)){
			return $m[0];
		}
	}
	function RecogniseAll($data){
		if(static::HOST_REGEX === null){
			return false;
		}
		$m = array();
		if(preg_match_all('#'.static::HOST_REGEX.'#i',$data,$m)){
			return $m[0];
		}
	}
	
	function ScoreValue(){
		return static::HOST_SCORE;
	}
	
	function CompressURL($url) {
		if(substr($url,0,4) != 'http'){
			$url = 'http://'.$url;
		}
		$path = parse_url($url,PHP_URL_PATH);
		$qs = parse_url($url,PHP_URL_QUERY);
		if($qs){
			$path .= '?'.$qs;
		}
		return $path;
	}
	static function addWWW($url){
		return 'http://www.'.$url;
	}
	function ExtractURL($url) {
		return static::addWWW(static::HOST_DOMAIN.$url);
	}
	function AppendFilename($url,$filename){
		return $url;
	}
	function Check($url){
		$F = new \HTTP\Fetch($url);
		$F->setTimeout(5);
		$F->setModule('Link Checker');
		$result = $F->Execute();
		
		$status = 'unknown';
		
		if($result->hasContent()){
			$status = $this->ValidateCheck($result->getContent(),$url);
		}
		
		if(!($status instanceof CheckReturn)){
			$status = new CheckReturn($status);
		}
		
		$status->setModule($this->getClassName());
		$status->setCompressedUrl($this->CompressURL($url));
		
		return $status;
	}
	function CheckURLs(array $urls){
		$obj = $this;
		$links = array();
		$callback = function($status,$url) use(&$links,$obj){
			//Set Status stuff
			$status->setModule($obj->getClassName());
			$status->setCompressedUrl($obj->CompressURL($url));
			
			//Add to array outside scope
			$links[$url] = $status;
		};
		
		$mh = new \HTTP\Multi();
		foreach($urls as $url){
			$this->CheckMulti($mh, $url, $callback);
		}
		
		$mh->ExecuteAndProcess();
		
		return $links;
	}
	function CheckMulti($mh,$url,$callback){
		$F = new \HTTP\Fetch($url);
		$F->setTimeout(5);
		$F->setModule('Link Checker');
		
		$obj = new HostMulti(get_called_class(), $url, $callback);
		
		$mh->Add($F,array($obj,'Callback'));
	}
	
	function getAbbr(){
		return static::HOST_ABBR;
	}
	
	function getClassName(){
		$c = get_called_class();
		$c = explode('\\',$c);
		return $c[count($c)-1];
	}
}