<?php
namespace Model\Database\DBAL\Adapter;

interface IConnection {
	function ping();
	
	/**
	 * is the MySQL server connected?
	 * @return boolean
	 */
	function isConnected();
	
	function toInstance();
	
	function reConnect();
	
	function close();
	
	function query($sql);
	
	function prepare($sql);
	
	function escape($string);
	
	/**
	 * Return the last MySQL error
	 */
	function error();
	
	/**
	 * Return the number of affected rows of the last MySQL query
	 */
	function affectedRows();
	
	/**
	 * @return string
	 */
	function __toString();
	
	static function fromArray(array $from);
}