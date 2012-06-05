<?php
namespace Model\Database\SQL;

use Model\Database\Model\TableReferenceInstance;

class UnLockTable extends Internal\StatementBase {
	function toSQL(){
		return 'UNLOCK TABLES';
	}
}