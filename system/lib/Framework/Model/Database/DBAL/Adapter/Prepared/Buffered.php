<?php
namespace Model\Database\DBAL\Adapter\Prepared;

use Model\Database\DBAL\Adapter\PreparedStatement;

class Buffered extends Common {
	function __construct($statement,PreparedStatement $p){
		$statement->store_result();
		parent::__construct($statement,$p);
	}
}