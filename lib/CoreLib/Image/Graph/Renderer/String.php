<?php
namespace Image\Graph\Renderer;
use Image\Graph\pChart\pChart;

class String extends RawRender {
	function Output(\Image\Graph\Schema\Graph $pChart){
		ob_start();
		parent::Output($pChart);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}
}