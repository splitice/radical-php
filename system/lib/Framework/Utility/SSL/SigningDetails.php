<?php
namespace Utility\SSL;

class SigningDetails {
	public $countryName = 'XX';
	public $stateOrProvinceName = 'State';
	public $localityName = 'SomewhereCity';
	public $organizationName = 'MySelf';
	public $organizationalUnitName = 'Whatever';
	public $commonName = 'mySelf';
	public $emailAddress = 'user@domain.com';
	
	public function toArray(){
		return (array)$this;
	}
}