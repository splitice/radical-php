<?php
namespace Utility\Image\Graph\Renderer;
use Utility\Image\Graph\Renderer\IRenderable;

class Kendo extends ImageGraph implements IRenderable {
	function output(\Utility\Image\Graph\Schema\Graph $schema){
		$data = array();
		foreach($schema->data as $axis=>$axis_data){
			foreach($axis_data as $k=>$v){
				if(isset($data[$k])){
					$data[$k][$axis] = $v;
				}else{
					$data[$k]=array($axis => $v);
				}
			}
		}
		return $data;
	}
}