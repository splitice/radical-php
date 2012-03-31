<?php
namespace CLI\Cron\Jobs\Interfaces;

interface ICronJob {
	function Execute(array $arguments);
	function getName();
}