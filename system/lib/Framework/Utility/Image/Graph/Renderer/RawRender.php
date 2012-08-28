<?php
namespace Utility\Image\Graph\Renderer;
use Utility\Image\Graph\pChart\pChart;
use Utility\Image\Graph\Renderer\IRenderable;

class RawRender extends ImageGraph implements IRenderable {
	function output(\Utility\Image\Graph\Schema\Graph $schema){
		$pChart = $this->_buildChart($schema);
		if(!$pChart->Render(null)){
			throw new \Exception('Failed to render image: '.error_get_last());
		}
	}
}