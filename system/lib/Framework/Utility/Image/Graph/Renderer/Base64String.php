<?php
namespace Utility\Image\Graph\Renderer;
use Utility\Image\Graph\pChart\pChart;

class Base64String extends String {
	function output(\Utility\Image\Graph\Schema\Graph $pChart){
		$string = parent::Output($pChart);
		return base64_encode($string);
	}
}