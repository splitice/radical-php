<?php
namespace Web\Session\Storage;

interface ISessionStorage extends \ArrayAccess {
	function lock_open();
	function lock_close();
}