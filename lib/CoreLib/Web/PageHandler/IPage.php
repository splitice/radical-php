<?php
namespace Web\PageHandler;

interface IPage {
	//function GET();
	//function POST();
	function Execute();
	function can($m);
}