<?php
namespace Utility\SSL;

class X509 {
	static function CheckPair($cert, $key, $passphrase = null){
		if(openssl_pkey_get_private($key,$passphrase) === false){
			return false;
		}
		return openssl_x509_check_private_key($cert,$key);
	}
	
	static function Generate(SigningDetails $dn, $privkeypass = null, $numberofdays = 365){
		$privkey = openssl_pkey_new();
		$csr = openssl_csr_new($dn->toArray(), $privkey);
		$sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
		openssl_x509_export($sscert, $publickey);
		$privatekey = null;
		if(!openssl_pkey_export($privkey, $privatekey, $privkeypass)){
			throw new \Exception('Private key generatio failed');
		}
		/*$csrStr = null;
		if(!openssl_csr_export($csr, $csrStr)){
			throw new \Exception('CSR generation failed');
		}*/
		
		return new X509CertificatePair($publickey, $privatekey);
	}
}