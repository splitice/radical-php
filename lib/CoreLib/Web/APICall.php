<?php
namespace Web;

class APICall {
	protected $server;
	
	function __construct($server){
		$this->server = $server;
	}
	/**
	 * convert xml string to php array - useful to get a serializable value
	 *
	 * @param string $xmlstr
	 * @return array
	 * @author Adrien aka Gaarf
	 */
	static function xmlstr_to_array($xmlstr) {
		$doc = new \DOMDocument();
		$doc->loadXML($xmlstr);
		return static::domnode_to_array($doc->documentElement);
	}
	static function domnode_to_array($node) {
		$output = array();
		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE:
			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;
			case XML_ELEMENT_NODE:
				for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
					$child = $node->childNodes->item($i);
					$v = static::domnode_to_array($child);
					if(isset($child->tagName)) {
						$t = $child->tagName;
						if(!isset($output[$t])) {
							$output[$t] = array();
						}
						$output[$t][] = $v;
					}
					elseif($v) {
						$output = (string) $v;
					}
				}
				if(is_array($output)) {
					if($node->attributes->length) {
						$a = array();
						foreach($node->attributes as $attrName => $attrNode) {
							$a[$attrName] = (string) $attrNode->value;
						}
						$output['@attributes'] = $a;
					}
					foreach ($output as $t => $v) {
						if(is_array($v) && count($v)==1 && $t!='@attributes') {
							$output[$t] = $v[0];
						}
					}
				}
				break;
		}
		return $output;
	}
	function Call($module,$method,$argument,$type='ps'){
		$url = rtrim($this->server,'/').'/'.$module.'/'.$method.'.'.$type.'?'.http_build_query($argument);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$text = curl_exec($ch);
		
		$data = null;
		switch($type){
			case 'json':
				$data = json_decode($text);
				break;
				
			case 'xml':
				$data = static::xmlstr_to_array($text);
				break;
				
			case 'ps':
				$data = unserialize($text);
				break;
		}
		
		if(isset($data['response'])){
			return $data['response'];
		}
		
		if(isset($data['error'])){
			throw new \Exception($data['error']);
		}
	}
}