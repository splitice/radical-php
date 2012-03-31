<?php
namespace Image\Graph\Source;
use Database\DBAL\Fetch;
use Database\DBAL\Result;
use Graph\pChart\pData;
use Graph\pChart\pChart;

class Database extends Internal\GraphBase {
	/**
	 * @var Result
	 */
	protected $res;
	
	function __construct(Result $res,$format = 'number',$title = null){
		$this->res = $res;
		parent::__construct($format,$title);
	}
	private $data;
	function getData(){
		if($this->data){
			return $this->data;
		}
		
		$data = array();
		foreach($this->res->Fetch(Fetch::ALL_ASSOC) as $i=>$row){
			foreach($row as $k=>$v){
				if($k{0} == '|'){//Skip
					continue;
				}
				
				if($k{0} == '*'){
					$k2 = '|'.substr($k,1);
					if(isset($row[$k2])){
						$k = $row[$k2];
					}
				}
				
				if(!isset($data[$k])){
					$data[$k] = array();
				}
				if(isset($row['X'])){
					$data[$k][$row['X']] = (int)$v;
				}else{
					$data[$k][] = (int)$v;
				}
			}
		}
		$this->data = $data;
		return $this->data;
	}
}