<?php
namespace Web\Session\User;

interface IUserAdmin extends IUser {
	function isAdmin();
}