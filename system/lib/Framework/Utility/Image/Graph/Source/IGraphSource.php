<?php
namespace Utility\Image\Graph\Source;
use Utility\Image\Graph\Renderer\IRenderable;

interface IGraphSource {
	function Setup($width,$height);
	function Draw(IRenderable $R);
}