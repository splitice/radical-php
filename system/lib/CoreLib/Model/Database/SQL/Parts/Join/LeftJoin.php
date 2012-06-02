<?php
namespace Database\SQL\Parts\Join;

use Database\SQL\Parts\Internal;

class LeftJoin extends Internal\JoinPartBase {
	const JOIN_TYPE = 'LEFT';
}