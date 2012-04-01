<?php
namespace Video\Metadata\Internal;

abstract class MetadataBase {
	function supports($k){
		foreach($this->supports as $vv){
			if(strtolower($vv) == $k){
				return $vv;
			}
		}
	}
}