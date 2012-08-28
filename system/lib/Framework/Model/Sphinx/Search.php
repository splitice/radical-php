<?php
namespace Model\Sphinx;

class Search extends \DB { 
	static function connect(Connection $connection) {
		return parent::Connect($connection);
	}
}