<?php
namespace Web\Page\Handler;

interface IPage {
	function Execute();
	function can($m);
}