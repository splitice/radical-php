<?php
namespace Image\Graph\Source;
use Database\DBAL\Result;
use Graph\pChart\pData;
use Graph\pChart\pChart;

class DatabaseZeroed extends Database {
	function getData(){
		$data = parent::getData();
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