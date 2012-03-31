<?php
namespace Image\Graph\Renderer;
use Image\Graph\pChart\pChart;

class Output extends RawRender {
	function Output(\Image\Graph\Schema\Graph $pChart){
		parent::Output($pChart);
		header('Content-Type: image/png',true);
	}
}