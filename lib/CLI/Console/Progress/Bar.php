<?php
namespace CLI\Console\Progress;

class Bar extends \Core\Object implements \Serializable {
	public $progress = 0;
	private $id;
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	static function __set_state($array){
		return new self($array['progress']);
	}
	
	public function serialize() {
		return $this->progress.'|'.$this->id;
	}
	public function unserialize($data) {
		list($progress,$id) = explode('|',$data);
		$this->id = (int)$id;
		$this->progress = (float)$progress;
	}
	
	/**
	 * @var Container
	 */
	protected $container;
	
	function __construct($progress = 0){
		$this->progress = $progress;
		$this->id = md5(microtime(true).microtime(true));
		$this->container = Container::getInstance();
		$this->container->Add($this);
	}
	
	function setProgress($percent){
		if($percent < 0) $percent = 0;
		if($percent > 100) $percent = 100;
		
		$percent = round($percent,0);
		if($percent != $this->progress){
			$this->progress = $percent;
			
			if($this->container){
				$this->container->PushProgress($this->id,$this->progress);
				$this->container->Render();
				
				if($percent == 100){
					$this->container->Done($this);
					$this->container = null;
				}
			}
		}
	}
	
	function Done(){
		if($this->container){
			$this->container->Done($this);
			$this->container = null;
		}
	}
	
	function Render($width){
		//Desperation
		if(!$width){
			return '';
		}
		if($width == 1){
			if($this->progress == 100){
				return '*';
			}
			return floor($this->progress/2);
		}
		if($width == 2){
			if($this->progress == 100){
				return '**';
			}
			return floor($this->progress);
		}
		if($width == 3){
			if($this->progress == 100){
				return ' * ';
			}
			return floor($this->progress).'%';
		}
		if($width == 4 || $width == 5){
			return str_pad(floor($this->progress).'%',$width,' ',STR_PAD_BOTH);
		}
		if($width == 6){
			return '['.str_pad(floor($this->progress),4,' ',STR_PAD_BOTH).']';
		}
		
		$width -= 2;
		$blockCount = round($width*$this->progress/100,0);

		$blocks = '';
		if($blockCount > 0){
			$blocks = str_repeat('=', $blockCount);
		}
		
		$repeat = $width-$blockCount;
		if($repeat > 0){
			$blocks .= str_repeat(' ', $repeat);
		}
		
		$blocks = '['.$blocks.']';
		return $blocks;
	}
}