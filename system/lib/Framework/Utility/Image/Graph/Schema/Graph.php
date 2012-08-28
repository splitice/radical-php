<?php
namespace Utility\Image\Graph\Schema;

use Utility\Image\Graph\Renderer\IRenderable;

class Graph extends Internal\SchemaBase {
	public $data;
	public $axis = array();
	public $title;
	public $hover;
	public $font;
	public $grid = 1;
	public $color;
	public $box;
	public $legend;
	public $symbol = 'circle';
	public $type = 'plot';
	
	function __construct($data = array(),$type = 'plot'){
		$this->data = new Dataset($data);
		$this->axis['X'] = new Axis();
		$this->axis['Y'] = new Axis();
		$this->box = new Box();
		$this->type = $type;
	}
	
	function render(IRenderable $renderer){
		return $renderer->Output($this);
	}
}