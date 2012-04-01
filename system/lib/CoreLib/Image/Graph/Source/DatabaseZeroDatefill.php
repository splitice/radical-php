<?php
namespace Image\Graph\Source;
use Database\DBAL\Result;
use Graph\pChart\pData;
use Graph\pChart\pChart;

class DatabaseZeroDatefill extends DatabaseZeroed {
	const DAY = 86400;
	
	protected $dateRange;
	
	function __construct(Result $res,$title = null,$date_range = null){
		parent::__construct($res,'date',$title);
		$this->dateRange = $date_range;
	}
	private function minDate(array $data){
		if(!$this->dateRange) return min($data);
		return $this->dateRange[0];
	}
	private function maxDate(array $data){
		if(!$this->dateRange) return max($data);
		return $this->dateRange[1];
	}
	function getData(){
		$data = parent::getData();
		if(!isset($data['X'])){
			return $data;
		}
		$min_date = $this->minDate($data['X']);
		$max_date = $this->maxDate($data['X']);
		for($i = $min_date; $i<=$max_date; $i+=static::DAY){
			$data['X'][$i] = $i;
		}
		
		foreach($data as $k=>$array){
			foreach($array as $key=>$value){
				foreach($data as $k2 => $array2){
					if(!isset($data[$k2][$key])){
						$data[$k2][$key] = 0;
					}
				}
			}
		}
		foreach($data as $k=>$v){
			ksort($data[$k]);
		}
		return $data;
	}
}