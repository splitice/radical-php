<?php
namespace Utility\Image\Graph\Source;
use Utility\Image\Graph\Renderer\IRenderable;

interface IGraphSource {
	function setup($width,$height);
	function draw(IRenderable $R);
}