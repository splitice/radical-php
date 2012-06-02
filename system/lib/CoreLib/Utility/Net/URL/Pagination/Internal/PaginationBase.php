<?php
namespace Net\URL\Pagination\Internal;

use Database\SQL\SelectStatement;

use Database\IToSQL;

use Net\URL\Pagination\Template\IPaginationTemplate;

abstract class PaginationBase extends \Core\Object {
	protected $url;
	public $nofollow;
	protected $current;
	
	function __construct($url){
		$this->url = $url;
	}
	
	function getLimit($num){
		$sql = new SelectStatement();
		$sql->limit((($this->current-1)*$num),$num);
		return $sql;
	}
	
	/**
	 * @return the $current
	 */
	public function getCurrent() {
		return $this->current;
	}

	abstract function toURL($i);
	private function _noFollow($i){
		if($this->nofollow){
			if($this->nofollow()){
				return ' rel="nofollow"';
			}
		}
		return '';
	}
	function Output($last,IPaginationTemplate $template){
		if($last == 1){
			echo $template->onePage();
			return;
		}
		$istart=max($this->current-5,1);
		if($this->current>1){
			echo $template->prevLink($this, $this->current-1);
		}
		$f=min($last,$this->current+5);
		for($i=$istart;$i<=$f;++$i){
			echo $template->pageLink($this, $i, $this->current==$i);
		}
		if($istart>1 && $f!=$last){
			echo $template->lastLink($this, $last);
		}
		if($this->current<$last){
			echo $template->nextLink($this, $this->current+1);
		}
	}
}