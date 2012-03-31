<?php
namespace Basic\String;
use COM;

class UUID {
	/**
	 * UUID-related constant. Clears all bits of version byte (`00001111`).
	 */
	const UUID_CLEAR_VER = 15;
	
	/**
	 * UUID constant that sets the version bit for generated UUIDs (`01000000`).
	 */
	const UUID_VERSION_4 = 64;
	
	/**
	 * Clears relevant bits of variant byte (`00111111`).
	 */
	const UUID_CLEAR_VAR = 63;
	
	/**
	 * The RFC 4122 variant (`10000000`).
	 */
	const UUID_VAR_RFC = 128;
	
	/**
	 * Generates an RFC 4122-compliant version 4 UUID.
	 *
	 * @return string The string representation of an RFC 4122-compliant, version 4 UUID.
	 * @link http://www.ietf.org/rfc/rfc4122.txt RFC 4122: UUID URN Namespace
	 */
	public static function Generate() {
		$uuid = Random::GenerateBytes(16);
		$uuid[6] = chr(ord($uuid[6]) & static::UUID_CLEAR_VER | static::UUID_VERSION_4);
		$uuid[8] = chr(ord($uuid[8]) & static::UUID_CLEAR_VAR | static::UUID_VAR_RFC);

		return join('-', array(
			bin2hex(substr($uuid, 0, 4)),
			bin2hex(substr($uuid, 4, 2)),
			bin2hex(substr($uuid, 6, 2)),
			bin2hex(substr($uuid, 8, 2)),
			bin2hex(substr($uuid, 10, 6))
		));
	}
}