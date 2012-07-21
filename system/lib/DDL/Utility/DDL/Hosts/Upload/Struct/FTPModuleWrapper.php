<?php
namespace Utility\DDL\Hosts\Upload\Struct;
use Utility\DDL\Hosts\Upload\Interfaces\IUploadHost;
use Utility\DDL\Hosts\Upload\Internal\FTPHostBase;

class FTPModuleWrapper implements IUploadHost {
	private $ftp;
	
	function __construct(FTPHostBase $ftp){
		$this->ftp = $ftp;
	}
	function Upload($file){
		return $this->ftp->FTPUpload($file);
	}
}