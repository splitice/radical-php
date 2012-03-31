<?php
namespace HTML\Form\Builder\Adapter;

interface IAdapter {
	function getAll($sql = null);
	function fromId($id);
	static function is($obj);
}