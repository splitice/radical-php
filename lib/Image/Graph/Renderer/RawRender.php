<?php
namespace Image\Graph\Renderer;
use Image\Graph\pChart\pChart;
use Image\Graph\Renderer\IRenderable;

class RawRender extends ImageGraph implements IRenderable {
	function Output(\Image\Graph\Schema\Graph $schema){
		$pChart = $this->_buildChart($schema);
		$pChart->Render(null);
	}
}