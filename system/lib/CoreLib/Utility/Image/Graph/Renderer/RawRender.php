<?php
namespace Image\Graph\Renderer;
use Image\Graph\pChart\pChart;
use Image\Graph\Renderer\IRenderable;

class RawRender extends ImageGraph implements IRenderable {
	function Output(\Image\Graph\Schema\Graph $schema){
		$pChart = $this->_buildChart($schema);
		if(!$pChart->Render(null)){
			throw new \Exception('Failed to render image: '.error_get_last());
		}
	}
}