<?php
namespace CLI\Daemon\Module\Interfaces;

interface IModuleJob {
	function execute(array $arguments);
	function getName();
}