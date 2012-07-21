<?php
namespace CLI\Threading\Internal;

abstract class SemaphoreHelpers {
	protected function _validateTickets($tickets) {
		if ($tickets <= 0) {
			throw new \Exception ( 'tickets to release must be 1 or greater' );
		}
	}
	public static function fromObject($object) {
		return new static ( crc32 ( spl_object_hash ( $object ) ) );
	}
}