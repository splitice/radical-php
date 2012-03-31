<?php
namespace Net\ExternalInterfaces\Sphinx;

class Search extends \DB { 
	static function Connect(Connection $connection) {
		return parent::Connect($connection);
	}
}