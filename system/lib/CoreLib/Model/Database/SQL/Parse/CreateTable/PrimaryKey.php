<?php
namespace Database\SQL\Parse\CreateTable;

class PrimaryKey extends IndexStatement {
	function __construct($keys) {
		parent::__construct('PRIMARY','PRIMARY',$keys,'');
	}
}