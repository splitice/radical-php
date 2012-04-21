<?php
namespace Web\Session\Authentication\Source;

use Database\DynamicTypes\Password;
use Database\Model\TableReferenceInstance;

class Database extends MultipleDatabase {
	function __construct(TableReferenceInstance $table){
		parent::__construct($table);
	}
}