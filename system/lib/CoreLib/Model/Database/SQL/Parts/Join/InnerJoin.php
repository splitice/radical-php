<?php
namespace Database\SQL\Parts\Join;

use Database\SQL\Parts\Internal;

class InnerJoin extends Internal\JoinPartBase {
	const JOIN_TYPE = 'INNER';
}