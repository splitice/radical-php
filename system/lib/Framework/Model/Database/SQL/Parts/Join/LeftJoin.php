<?php
namespace Model\Database\SQL\Parts\Join;

use Model\Database\SQL\Parts\Internal;

class LeftJoin extends Internal\JoinPartBase {
	const JOIN_TYPE = 'LEFT';
}