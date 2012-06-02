<?php
namespace Database\DBAL;

class Result {
	private $result;
	public $affected_rows;
	static $fetchAllSupport;
	
	function __construct(\MySQLi_Result $result,Adapter\Instance $db){
		$this->result = $result;
		$this->affected_rows = $db->AffectedRows();
		if(null === static::$fetchAllSupport){
			static::$fetchAllSupport = method_exists($result,'fetch_all');
		}
	}
	
	/**
	 * Perfrm MySQL Fetch
	 * @param mysqli_result $res
	 * @param int $format Format to fetch and return
	 * @throws \DB\Exception\FetchNotAResult
	 * @return mixed
	 */
	function Fetch($format = Fetch::ASSOC, \Cast\ICast $cast=null) {
		//Do something for each format
		switch($format){
			case Fetch::ASSOC:
				$return = $this->result->fetch_assoc();
				if(!$return){
					return false;
				}
				$return = new Row($return);
				break;

			case Fetch::NUM:
				$return = $this->result->fetch_array ( MYSQLI_NUM );
				if(!$return){
					return false;
				}
				break;

			case Fetch::FIRST:
				$row = $this->result->fetch_array ( MYSQLI_NUM );
				if (! $row) {
					return false;
				}
				$return = $row [0];
				break;

			case Fetch::ALL_ASSOC:
				if(static::$fetchAllSupport){
					$return = array();
					foreach($this->result->fetch_all( MYSQLI_ASSOC) as $a){
						$return[] = new Row($a);
					}
					return $return;
				}
				$return = array ();
				while ( $row = $this->result->fetch_assoc ( ) ) {
					$return [] = new Row($row);
				}
				break;

			case Fetch::ALL_NUM:
				if(static::$fetchAllSupport){
					return $thisv->result->fetch_all( MYSQLI_NUM );
				}
				$return = array ();
				while ( $row = $this->result->fetch_array ( ) ) {
					$return [] = $row;
				}
				break;

			case Fetch::ALL_SINGLE:
				if(static::$fetchAllSupport){
					$return = array ();
					foreach ( $this->result->fetch_all(MYSQLI_NUM) as $row ) {
						$return [] = $row [0];
					}
					return $return;
				}
				$return = array ();
				while ( $row = $this->result->fetch_array ( ) ) {
					$return [] = $row[0];
				}
				break;
		}
		
		if($cast !== null){
			return $cast->Cast($return);
		}
		return $return;
	}
	
	function FetchAll($mode = Fetch::ALL_ASSOC){
		return $this->Fetch($mode);
	}
	
	/**
	 * Perform MySQL fetch and execute $callback on it returning the result
	 * @param function $callback
	 * @param DB\Fetch:: $format
	 * @return Array <int, mixed>
	 */
	function FetchCallback($callback, $format = Fetch::ALL_ASSOC) {
		return array_map($callback,$this->Fetch ( $format ));
	}
	
	function __destruct(){
		$this->result->free();
		$this->result = null;
	}
	
	function __get($name){
		if(isset($this->result->$name)){
			return $this->result->$name;
		}
		throw new \BadMethodCallException($name.' field doesnt exist');
	}
}