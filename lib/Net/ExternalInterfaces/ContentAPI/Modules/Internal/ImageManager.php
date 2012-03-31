<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules\Internal;
use \Net\ExternalInterfaces\ContentAPI\Interfaces;
use \Basic\ArrayLib\Object\ArrayObject;

class ImageManager extends ArrayObject implements Interfaces\IExportable {
	const DATA_IMAGE = 0;
	const DATA_TAGS = 1;
	
	private function _encode($image,$tags){
		return array(self::DATA_IMAGE=>$image,self::DATA_TAGS=>$tags);
	}
	private function _decode($data){
		return $data[self::DATA_IMAGE];
	}
	function Add($v,$tags){
		return parent::Add($this->_encode($v,$tags));
	}
	function Set($v,$tags){
		return parent::Set($this->_encode($v,$tags));
	}
	function Get($k){
		return $this->_decode(parent::Get($k));
	}
	function getByTag($tag){
		if(!is_array($tag)){
			$tag = array($tag);
		}

		$ret = array();
		foreach($this->data as $d){
			$ok = true;
			foreach($tag as $t){
				if(!in_array($t, $d[self::DATA_TAGS])){
					$ok = false;
				}
			}
			if($ok){
				$ret[] = $d[self::DATA_IMAGE];
			}
		}
		return $ret;
	}
	function toExport(){
		return $this->asArray();
	}
}