<?php
namespace Model\Database\DBAL\Prepared;

use Model\Database\DBAL\Adapter\PreparedStatement;

class UnBuffered extends Common {
	function __construct($statement,PreparedStatement $p){
		//Register with $this->db using weakmap
		parent::__construct($statement,$p);
	}
}