<?php
namespace Web\Session\Authentication;

interface IAuthenticator {
	function Authenticate();
	function Init();
}