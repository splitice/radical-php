<?php
namespace Web\Session\Authentication\Source;

use Model\Database\DynamicTypes\Password;
use Model\Database\Model\TableReferenceInstance;

class Database extends MultipleDatabase {
	function __construct(TableReferenceInstance $table){
		parent::__construct($table);
	}
}