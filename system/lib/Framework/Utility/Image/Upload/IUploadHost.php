<?php
namespace Utility\Image\Upload;

interface IUploadHost {
	static function login($username = null,$password = null);
	function upload($file,$file_type,$size='500x500');
}