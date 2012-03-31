<?php
namespace Image\Graph\Source\Internal;
use Image\Graph\Source\IGraphSource;

use Image\Graph\Renderer\IRenderable;

abstract class GraphBase implements IGraphSource {
	public $schema;
	
	function __construct($format = 'number',$title = null){
		$this->schema = new \Image\Graph\Schema\Graph($data);
		$this->schema->title = $title;
		$this->schema->axis['X']->format = $format;
	}
	
	abstract function getData();
	
	function Setup($width,$height){	
		$this->schema->box->width = $width;
		$this->schema->box->height = $height;
	}
	function Draw(IRenderable $R){
		$this->schema->data->SetAll($this->getData());
		
		return $R->Output($this->schema);
	}
}