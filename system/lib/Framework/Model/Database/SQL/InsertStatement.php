<?php
namespace Model\Database\SQL;

/*
http://dev.mysql.com/doc/refman/5.5/en/insert.html

INSERT [LOW_PRIORITY | DELAYED | HIGH_PRIORITY] [IGNORE]
    [INTO] tbl_name
    {[SET col_name={expr | DEFAULT}, ...]|[VALUES({expr | DEFAULT}, ...)]}
    [ ON DUPLICATE KEY UPDATE
      col_name=expr
        [, col_name=expr] ... ]
        
INSERT [LOW_PRIORITY | HIGH_PRIORITY] [IGNORE]
    [INTO] tbl_name [(col_name,...)]
    SELECT ...
    [ ON DUPLICATE KEY UPDATE col_name=expr, ... ]
 */
class InsertStatement extends Internal\StatementBase {
	protected $table;
	protected $values;
	protected $ignore;
	
	function __construct($table,$values,$ignore=false){
		$this->table = $table;
		$this->values = $values;
		$this->ignore = $ignore;
	}
	
	private function _appendPart(){
		$append = '';
		if(is_string($this->ignore)){
			$append = ' ON DUPLICATE KEY UPDATE '.$this->ignore;
			$this->ignore = false;
		}elseif($this->ignore == -1){
			$append = ' ON DUPLICATE KEY UPDATE ';
			$join = array();
			foreach($this->values as $k=>$v){
				if($v!==null)
					$join [] = '`'.$k.'`=VALUES(`'.$k.'`)';
			}
			$append .= implode(',',$join);
		}
		
		return $append;
	}
	
	private $sql;
	function toSQL(){
		if($this->sql){
			return $this->sql;
		}
		
		//Build Append Part
		$append = $this->_appendPart();
		
		//Build Query
		$sql = 'INSERT ' . (($this->ignore===true) ? 'IGNORE ' : '');
		$sql .= 'INTO `' . $this->table . '` (`' . implode ( '`,`', array_keys ( $this->values ) ) . '`) ';
		$sql .= 'VALUES(' . \DB::A ( $this->values ) . ')';
		$sql .= $append;
		
		$this->sql = $sql;
		return $sql;
	}
}