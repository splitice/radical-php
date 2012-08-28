<?php
namespace Utility\Image\Graph\Renderer;
use Utility\Image\Graph\pChart\pChart;

class Output extends RawRender {
	function output(\Utility\Image\Graph\Schema\Graph $pChart){
		parent::Output($pChart);
		header('Content-Type: image/png',true);
	}
}