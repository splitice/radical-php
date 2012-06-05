<?php
namespace Tests\Database\SQL\Parts;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class Having extends Where {
	const PART_NAME = 'HAVING';
	
	protected function _class(){
		return '\\Database\\SQL\\Parts\\Having';
	}
}