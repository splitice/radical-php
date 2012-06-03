<?php
namespace Core\ErrorHandling;

interface IErrorException {
	function getMessage();
	function getPage();
}