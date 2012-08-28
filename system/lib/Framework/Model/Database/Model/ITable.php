<?php
namespace Model\Database\Model;

interface ITable {
	function toSQL($in = null);
	function update();
	function delete();
	function getIdentifyingSQL();
	function insert();
	
	/* Static Functions */
	static function getAll($sql = '');
	static function fromFields(array $fields);
	static function fromId($id);
	static function fromSQL($res);
	
	static function exists();
	static function create($data,$prefix=false);
}