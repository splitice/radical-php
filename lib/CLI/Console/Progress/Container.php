<?php
namespace CLI\Console\Progress;

use CLI\Thread;

use CLI\Console\Details;

class Container extends \Core\Object {
	private $bars = array();
	private $filter;
	static $instance;
	
	function __construct(){
		static::$instance = $this;
		
		$this->filter = new \Output\Filter(array($this,'outputBuffer'));
	}
	
	static function getInstance(){
		if(!static::$instance){
			static::$instance = new static();
		}
		return static::$instance;
	}
	
	private function eraseLine(){
		$width = Details::getWidth();
		$output = "\r".str_repeat(' ', $width)."\r";
		return $output;
	}
	
	protected function Close(){
		//debug_print_backtrace();
		echo $this->eraseLine();
		
		if(ob_get_level()) ob_flush();
		
		//Remove Output Buffering
		\Output\Filter::deRegister($this->filter);
		
		//Remove instance
		static::$instance = null;
	}
	
	function PushProgress($barId,$progress){
		if($this->masterThread(__FUNCTION__,array($barId,$progress))){
			foreach($this->bars as $b){
				if($b->getId() == $barId){
					$b->progress = $progress;
				}
			}
		}
	}
	
	private function masterThread($function,$arguments = array()){
		if(Thread::$self){
			$parent = Thread::$self->parent;
			if($parent){
				$code = get_called_class().'::getInstance()->'.$function.'(';
				foreach($arguments as $k=>$a){
					if($k){
						$code.= ',';
					}
					$code .= 'unserialize('.var_export(serialize($a),true).')';
				}
				$code .= ');';
				
				$msg = new \CLI\Threading\Messages\EvalMessage($code);
				$msg->Send($parent);
				
				return false;
			}
		}
		return true;
	}
	
	function Add(Bar $bar){
		if($this->masterThread(__FUNCTION__,array($bar))){
			$this->bars[] = $bar;
		}
	}
	
	function Done(Bar $bar){
		if($this->masterThread(__FUNCTION__,array($bar))){		
			//Remove
			foreach($this->bars as $k=>$v){
				if($v->getId() == $bar->getId()){
					unset($this->bars[$k]);
				}
			}
			
			//Re-Order
			$this->bars = array_values($this->bars);
			
			//Is close time?
			if(!$this->bars){
				$this->Close();
			}
		}
	}
	
	private function _Render(){
		if(!$this->bars){
			return $this->eraseLine();
		}
		
		$width = Details::getWidth();
		
		//Calculate the characters per progress bar
		$widthPerBar = floor(($width+2-(2*count($this->bars)))/(count($this->bars)));
		
		//Render Bars
		$bars = '';
		foreach($this->bars as $k=>$b){
			if($b instanceof Bar){//Just for syntax highlighting
				if($k != 0){
					$bars .= ' ';
				}
				$bars .= $b->Render($widthPerBar);
			}
		}
		return $bars;
		
	}
	
	function Render(){
		if($this->masterThread(__FUNCTION__)){			
			//Render Bars
			$bars = $this->_Render();

			//End Output Buffering
			\Output\Filter::deRegister($this->filter);
			
			//Output Bars
			echo $this->eraseLine(),$bars;
			
			//Re-install Output buffering
			\Output\Filter::Register($this->filter);
		}
	}
	
	function outputBuffer($data){
		$output = $this->eraseLine();
		if($data){
			$output .= rtrim($data,"\r\n")."\n";
		}
		$output .= $this->_Render();
		return $output;
	}
}