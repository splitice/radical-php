<?php
namespace Image\Graph\Renderer;
use Image\Graph\pChart\pChart;
use Image\Graph\Renderer\IRenderable;

class JSON extends ImageGraph implements IRenderable {
	function Output(\Image\Graph\Schema\Graph $schema){
		return $schema;
	}
}