<?php
namespace Basic\Cryptography;

use Basic\Cryptography\Internal\HashBase;

class Blowfish implements HashTypes\ITwoWayEncryption {
	const IV = '12345678';
	
	public static function Encode($cleartext, $key, $iv = self::IV){
		$cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		
		// 128-bit blowfish encryption
		if (mcrypt_generic_init($cipher, $key, $iv) != -1)
		{
			// PHP pads with NULL bytes if $cleartext is not a multiple of the block size..
			$cipherText = mcrypt_generic($cipher, $cleartext);
			mcrypt_generic_deinit($cipher);
			
			return $cipherText;
		}
	}
	public static function Decode($text, $key, $iv = self::IV){
		$cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		
		// 128-bit blowfish decryption
		if (mcrypt_generic_init($cipher, $key, $iv) != -1)
		{
			// PHP pads with NULL bytes if $text is not a multiple of the block size..
			$clearText = mdecrypt_generic($cipher, $text);
			mcrypt_generic_deinit($cipher);
			
			return $clearText;
		}
	}
}