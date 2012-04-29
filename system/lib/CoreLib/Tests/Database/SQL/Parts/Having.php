<?php
namespace Tests\Database\SQL\Parts;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Having extends Where {
	const PART_NAME = 'HAVING';
	
	protected function _class(){
		return '\\Database\\SQL\\Parts\\Having';
	}
}