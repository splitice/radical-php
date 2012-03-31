<?php
namespace DDL\Hosts\API\Internal;

class FSapi {
	static function getConnectURL(){
		return 'http://'.static::URL;
	}
	static function buildCall($module,$method,$data){
		$data['method'] = $method;
		$data['format'] = 'json';
		
		$qs = '';
		$t = array();
		foreach($data as $k=>$v){
			$t[$k] = urlencode($k).'='.urlencode($v);
		}
		$qs = implode('&',$t);
		
		$url = self::getConnectURL().'/'.$module.'?'.$qs;

		return $url;
	}
	static function buildRequest($module,$method,$data){
		$url = self::buildCall($module, $method, $data);
		$request = new \HTTP\Fetch($url);
		return $request;
	}
	static function Execute($module, $method, $data){
		$request = self::buildRequest($module,$method,$data);
		$data = $request->Execute()->getPage()->getContent();
		$data = json_decode($data);
		return $data;
	}
	static function readResponse($data,$method){
		return $data->$method->response;
	}
	static function UploadURL($user,$pass){
		$request = array('u'=>$user,'p'=>$pass);
		$data = self::Execute('upload', 'getUploadUrl', $request);
		if(self::isError($data)){
			return false;
		}
		if(!isset($data->FSApi_Upload)){
			return;
		}
		$base = $data->FSApi_Upload;
		$data = static::readResponse($base,'getUploadUrl');
		
		if(isset($data->url)){
			return $data->url;
		}
	}
	static function isError($data){
		return (isset($data->FSApi->methodName->errors) && count($data->FSApi->methodName->errors));
	}
	static function LinkCheck($ids,$mh=null,$callback=null){
		$request = array();
		if(is_array($ids)){
			$request['ids'] = implode(',',$ids);
		}else{
			$request['ids'] = $ids;
		}

		if($mh==null){
			$data = self::Execute('link', 'getInfo', $request);
			return self::_LinkCheck($data);
		}else{
			$F = self::buildRequest('link', 'getInfo', $request);
			$obj = new FSapi\Multi($callback,$mh,$F);
			$mh->Add($F,array($obj,'Callback'));
		}
	}
	static function _LinkCheck($data){
		if(self::isError($data)){
			return array('status'=>'unknown');
		}
		$ret = array();
		if(!isset($data->FSApi_Link->getInfo->response->links)){
			return array('status'=>'unknown');
		}
		foreach($data->FSApi_Link->getInfo->response->links as $l){
			if($l->status == 'NOT_AVAILABLE'){
				$status = 'dead';
			}else{
				$status = 'ok';
			}
			$d = array();
			$d['status'] = $status;
			if($status == 'ok'){
				$d['filesize'] = $l->size;
				$d['filename'] = $l->filename;
			}
			$ret[$l->id] = $d; 
		}
		if(count($ret) == 1){
			$ret = array_values($ret);
			$ret = $ret[0];
		}
		return $ret;
	}
}