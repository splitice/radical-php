<?php
namespace Image\Graph\Schema;

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
	
	function __construct($data = array()){
		$this->data = new Dataset($data);
		$this->axis['X'] = new Axis();
		$this->axis['Y'] = new Axis();
		$this->box = new Box();
	}
}