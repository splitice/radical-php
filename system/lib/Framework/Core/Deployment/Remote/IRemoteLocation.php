<?php
namespace Core\Deployment\Remote;

interface IRemoteLocation {
	function writeFile($file,$data);
}