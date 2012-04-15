<?php
namespace Database\Search;

use Database\Model\Table\TableSet;

class Filter {
	static function Apply(TableSet $result, Search $search){
		$results = $search->Execute();
	}
}