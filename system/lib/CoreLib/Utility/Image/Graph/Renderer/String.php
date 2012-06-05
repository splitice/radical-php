<?php
namespace Utility\Image\Graph\Renderer;
use Utility\Image\Graph\pChart\pChart;

class String extends RawRender {
	function Output(\Utility\Image\Graph\Schema\Graph $pChart){
		ob_start();
		parent::Output($pChart);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}
}