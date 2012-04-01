<?php
namespace Database\ORM;

class Mappings {
	private $model;
	private $structure;
	
	function __construct(Model $model,$structure){
		$this->model = $model;
		$this->structure = $structure;
	}
	function stripPrefix($databaseField){
		$tblPrefix = $this->model->tableInfo['prefix'];
		$pLen = strlen($tblPrefix);
		$nLen = strlen($databaseField);
		if($nLen > $pLen){
			if(0 == substr_compare($databaseField, $tblPrefix, 0, $pLen)){
				return substr($databaseField,$pLen);
			}
		}
		return null;
	}
	function translateToObjective($databaseField,$prefixed = true){
		//strip the prefix if requested
		if($prefixed){
			$structure = $this->structure;
			
			//Check field exists in schema
			if(!isset($structure[$databaseField])){
				throw new \Exception('Database field doesnt exist in schema');
			}
			
			//Check if is reference
			if($relation = $structure[$databaseField]->getRelation()){
				//Get related mapping object and translate to Objective from that
				$relation = $relation->getReference();
				if(!$relation){
					throw new \Exception('Couldnt parse relation reference');
				}
				
				$rTableRef = $relation->getTableReference();
				
				$rInfo = $rTableRef->Info();
				
				$translated = rtrim($rInfo['prefix'],'_');
			}else{
				//Not reference, process normally.
				$translated = $this->stripPrefix($databaseField);
				if($translated === null){//Must be reference
					throw new \Exception('Database field "'.$databaseField.'" must be a reference.');
				}
			}
		}else{
			$translated = $name;
		}
		
		//translate database format (underscores) to objective format (camel case)
		for($i=0,$f=strlen($translated);$i<$f;++$i){
			if($translated{$i} == '_'){
				$translated = substr($translated,0,$i).ucfirst(substr($translated,$i+1));
				--$f;
			}
		}
		
		//return the translated result
		return $translated;
	}
	function translateToDatabase($objectiveField,$prefix=true){
		//translate camelcase to underscore format
		for($i=0,$f = strlen($objectiveField);$i<$f;++$i){
			if(ctype_upper($objectiveField{$i})){
				$objectiveField = substr($objectiveField,0,$i).'_'.strtolower($objectiveField{$i}).substr($objectiveField,$i+1);
				++$f;
				++$i;
			}
		}
		
		//Add prefix if wanted
		if($prefix){
			$objectiveField = $this->model->tableInfo['prefix'].$objectiveField;
		}
	}
	function translationArray(){
		$ret = array();
		foreach($this->structure as $field=>$column){
			$ret[$field] = $this->translateToObjective($field);
		}
		return $ret;
	}
}