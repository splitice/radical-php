<?php
namespace Utility\Format\SSL;

class X509 {
	static function CheckPair($cert, $key, $passphrase = null){
		if(openssl_pkey_get_private($key,$passphrase) === false){
			return false;
		}
		return openssl_x509_check_private_key($cert,$key);
	}
	
	static function Generate(SigningDetails $dn, $privkeypass = null, $numberofdays = 365){
		$privkey = openssl_pkey_new();
		$csr = openssl_csr_new($dn, $privkey);
		$sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
		openssl_x509_export($sscert, $publickey);
		$privatekey = null;
		openssl_pkey_export($privkey, $privatekey, $privkeypass);
		$csrStr = null;
		openssl_csr_export($csr, $csrStr);
		
		//openssl genrsa -des3 -out server.key 1024
		//openssl req -new -key server.key -out server.csr
		//openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt
		die(var_dump($privatekey,$csrStr));
	}
}