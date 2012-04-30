<?php
namespace Web\PageHandler;

interface IPage {
	function Execute();
	function can($m);
}