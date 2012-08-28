<?php
namespace CLI\Cron\Jobs\Interfaces;

interface ICronJob {
	function execute(array $arguments);
	function getName();
}