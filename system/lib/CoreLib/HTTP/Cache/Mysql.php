<?php
namespace HTTP\Cache;

use Database\Model\DynamicTableReference;
use Database\Model\DynamicTableInstance;

class Mysql extends DynamicTableInstance {
	function __construct(){
		parent::__construct('http_cache', 'hc_', get_called_class());
	}
	function toSQL($in=null){
		if(!$in) $in = get_object_vars($this);
		
		return parent::toSQL($in);
	}
	
	function toExport(){
		$data = parent::toExport();
		
		$info = array();
		foreach($data as $k=>$v){
			if(substr_compare($k, 'info', 0, 4) == 0){
				$info[substr(static::_reverseTranslateName(substr($k,4),false),1)] = $v;
				unset($data[$k]);
			}
		}
		return new \HTTP\Curl\Response($info, $data['response']);
	}
	
	static private function getTable(){
		$dt = new static();
		$dt->addId('id', 'char(32)');
		$dt->addField('url', 'blob');
		$dt->addField('response', 'blob');
		
		//Info fields
		$dt->addField('info_url', 'blob');
		$dt->addField('info_content_type', 'varchar(255)');
		$dt->addField('info_http_code', 'tinyint unsigned');
		$dt->addField('info_header_size', 'smallint unsigned');
		$dt->addField('info_request_size', 'int unsigned');
		$dt->addField('info_filetime', 'int unsigned');
		//$dt->addField('info_ssl_verify_result', '');
		$dt->addField('info_redirect_count', 'tinyint unsigned');
		$dt->addField('info_total_time', 'float');
		$dt->addField('info_namelookup_time', 'float');
		$dt->addField('info_connect_time', 'float');
		$dt->addField('info_pretransfer_time', 'float');
		$dt->addField('info_size_upload', 'int unsigned');
		$dt->addField('info_size_download', 'int unsigned');
		$dt->addField('info_speed_download', 'int unsigned');
		$dt->addField('info_speed_upload', 'int unsigned');
		$dt->addField('info_download_content_length', 'int unsigned');
		$dt->addField('info_upload_content_length', 'int unsigned');
		$dt->addField('info_starttransfer_time', 'int unsigned');
		$dt->addField('info_redirect_time', 'float');
		//$dt->addField('info_certinfo', '');
		$dt->addField('info_redirect_url', 'blob');
		
		$dt->addField('ttl', 'int unsigned');
		
		$dt->EnsureExists(true);
		return $dt;
	}
	static function Get($url){
		$table = static::getTable();
		$obj = $table->fromId(md5($url));
		if($obj){
			$data = $obj->toExport();
			
			return $data;
		}
	}
	static function Set(\HTTP\Curl\Response $data,$url,$ttl = 0){
		$table = static::getTable();
		
		$data = $data->toSQL();
		$data['id'] = md5($url);
		$data['url'] = $url;
		
		$data['ttl'] = $ttl;
		if($data['ttl'] != 0){
			$data['ttl'] += time();
		}
		
		$obj = $table->fromSQL($data);
		
		$obj->Insert();
	}
	static function TTL(){
		$table = static::getTable();
		if($table->Exists()){
			foreach($table->getAll(' WHERE hc_ttl<='.time()) as $k){
				$k->Delete();
			}
		}
	}
}