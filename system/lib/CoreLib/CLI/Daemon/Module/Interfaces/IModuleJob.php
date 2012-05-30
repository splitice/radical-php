<?php
namespace CLI\Daemon\Module\Interfaces;

interface IModuleJob {
	function Execute(array $arguments);
	function getName();
}