<?php
namespace Database\SQL\Parts\Expression;

use Database\SQL\Parts\Internal;

class In extends Internal\FunctionalPartBase implements IComparison {
	const PART_NAME = 'IN';
}