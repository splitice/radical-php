<?php
namespace DDL\Hosts\Upload\Struct;
use DDL\Hosts\Upload\Interfaces\IUploadHost;
use DDL\Hosts\Upload\Internal\FTPHostBase;

class FTPModuleWrapper implements IUploadHost {
	private $ftp;
	
	function __construct(FTPHostBase $ftp){
		$this->ftp = $ftp;
	}
	function Upload($file){
		return $this->ftp->FTPUpload($file);
	}
}