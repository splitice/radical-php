<?php
namespace Utility\Image\Graph\Renderer;
use Utility\Image\Graph\pChart\pChart;

interface IRenderable {
	function Output(\Utility\Image\Graph\Schema\Graph $pChart);
}