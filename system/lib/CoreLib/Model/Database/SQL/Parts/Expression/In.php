<?php
namespace Model\Database\SQL\Parts\Expression;

use Model\Database\SQL\Parts\Internal;

class In extends Internal\FunctionalPartBase implements IComparison {
	const PART_NAME = 'IN';
}