<?php
namespace Image\Upload;

interface IUploadHost {
	static function Login($username = null,$password = null);
	function Upload($file,$file_type,$size='500x500');
}