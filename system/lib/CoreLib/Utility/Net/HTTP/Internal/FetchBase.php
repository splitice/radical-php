<?php
namespace HTTP\Internal;

abstract class FetchBase {
	/*protected function FetchReturn($ch,$result,$LIGHTHTTPD_TEST_MODE){
		$info = curl_getinfo($ch);
		$ss = substr($info['http_code'],0,1);
		
		if($result === false || $result === null){//false single, null multi
			$ret = new \HTTP\ReturnVal\Error(curl_errno($ch), curl_error($ch));
		}else{
			
			if($info['http_code'] == '417' && $LIGHTHTTPD_TEST_MODE===false){//lighttpd bug
				return false;
			}
			
			if($ss == '2'){//2xx
				$ret = new \HTTP\ReturnVal\OK($result, $info);
			}elseif($ss == '3'){
				$final = new \HTTP\ReturnVal\OK($result, $info);
				$ret = new \HTTP\ReturnVal\Redirect($info['url'],$final);
			}elseif($info['http_code'] == '404'){
				$ret = new \HTTP\ReturnVal\FileNotFound($result, $info);
			}elseif($ss == '4'){
				$ret = new \HTTP\ReturnVal\AccessDenied($result, $info);
			}else{
				$ret = new \HTTP\ReturnVal\Other($result, $info);
			}
		}
		
		curl_close($ch);
		
		return $ret;
	}
	
	static function returnError($errno,$error){
		$ret = new \HTTP\ReturnVal\Error($errno,$error);
		
		return $ret;
	}*/
}