<?php
namespace Web\Session\Authentication\Source;

interface ISessionSource {
	function Login($username,$password);
	function isLoggedIn();
	function Logout();
}