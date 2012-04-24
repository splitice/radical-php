<?php
namespace Web\Admin\Modules;

interface IAdminModule {
	function getName();
	function getSubmodules();
	function toURL();
}