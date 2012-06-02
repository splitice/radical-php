<?php
namespace Utility\Image\Graph\Renderer;
use Image\Graph\pChart\pChart;

interface IRenderable {
	function Output(\Image\Graph\Schema\Graph $pChart);
}