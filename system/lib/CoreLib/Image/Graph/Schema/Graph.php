<?php
namespace Image\Graph\Schema;

use Image\Graph\Renderer\IRenderable;

class Graph extends Internal\SchemaBase {
	public $data;
	public $axis = array();
	public $title;
	public $hover;
	public $font;
	public $grid;
	public $color;
	public $box;
	public $legend;
	public $symbol = 'circle';
	public $type;
	
	function __construct($data = array(),$type = 'line'){
		$this->data = new Dataset($data);
		$this->axis['X'] = new Axis();
		$this->axis['Y'] = new Axis();
		$this->box = new Box();
		$this->type = $type;
	}
	
	function Render(IRenderable $renderer){
		return $renderer->Output($this);
	}
}