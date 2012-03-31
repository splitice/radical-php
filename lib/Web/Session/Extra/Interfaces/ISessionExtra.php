<?php
namespace Web\Session\Extra\Interfaces;

use Web\Session\Handler\Internal\ISessionHandler;

interface ISessionExtra {
	function __construct(ISessionHandler $handler);
}