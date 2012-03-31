<?php
namespace CLI\Threading;

interface IForkAction {
	function preFork();
	function postFork($preData);
}