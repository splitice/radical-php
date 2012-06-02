<?php
namespace Model\Database\Search;

use Model\Database\Model\Table\TableSet;

class Filter {
	static function Apply(TableSet $result, Search $search){
		$results = $search->Execute();
	}
}