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
	public $color = array('BCE02E','E0642E','2E97E0','E0D62E','B02EE0','E02E75','5CE02E','E0B02E');
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