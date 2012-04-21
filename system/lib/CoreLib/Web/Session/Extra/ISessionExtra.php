<?php
namespace Web\Session\Extra;

use Web\Session\Handler\Internal\ISessionHandler;

interface ISessionExtra {
	function __construct(ISessionHandler $handler);
}