<?php
namespace Model\Database\SQL\Parts\Join;

use Model\Database\SQL\Parts\Internal;

class InnerJoin extends Internal\JoinPartBase {
	const JOIN_TYPE = 'INNER';
}