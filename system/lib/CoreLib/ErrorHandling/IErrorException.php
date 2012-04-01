<?php
namespace ErrorHandling;

interface IErrorException {
	function getMessage();
	function getPage();
}