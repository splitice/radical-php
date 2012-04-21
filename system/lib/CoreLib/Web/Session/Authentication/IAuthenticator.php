<?php
namespace Web\Session\Authentication;

interface IAuthenticator {
	function Authenticate();
	function Init();
	function AuthenticationError($error = 'Username or Password Invalid');
}