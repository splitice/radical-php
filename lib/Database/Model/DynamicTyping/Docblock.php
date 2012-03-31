<?php
namespace Database\Model\DynamicTyping;

class Docblock extends \Debug\Docblock {
	/**
	 * List of supported docblock tags for the database system.
	 * Only applies to database variables / fields
	 *
	 * @var array
	 */
	public static $tags = array(
			'var'
	);
}