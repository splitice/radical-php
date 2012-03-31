<?php
namespace Web\Session\Handler\Internal;

interface ISessionHandler extends \ArrayAccess {
	function getId();
}