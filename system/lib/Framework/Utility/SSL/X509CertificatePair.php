<?php
namespace Utility\SSL;

class X509CertificatePair {
	public $public;
	public $private;
	
	function __construct($public,$private){
		$this->public = $public;
		$this->private = $private;
	}
	
	function validate($passphrase = null){
		return X509::CheckPair($this->public, $this->private, $passphrase);
	}
}