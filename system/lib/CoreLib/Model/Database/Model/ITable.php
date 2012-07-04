<?php
namespace Model\Database\Model;

interface ITable {
	function toSQL($in = null);
	function Update();
	function Delete();
	function getIdentifyingSQL();
	function Insert();
	
	/* Static Functions */
	static function getAll($sql = '');
	static function fromFields(array $fields);
	static function fromId($id);
	static function fromSQL($res);
	
	static function Exists();
	static function Create($data,$prefix=false);
}