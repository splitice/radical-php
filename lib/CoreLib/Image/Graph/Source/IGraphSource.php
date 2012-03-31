<?php
namespace Image\Graph\Source;
use Image\Graph\Renderer\IRenderable;

interface IGraphSource {
	function Setup($width,$height);
	function Draw(IRenderable $R);
}