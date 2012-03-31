<?php
namespace Web\Mobile;

class Detect {
	static function isMobile(){
		$mobile_browser = '0';
		
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}
		
		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}
		
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
				'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
				'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
				'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
				'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
				'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
				'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
				'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
				'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
				'wapr','webc','winw','winw','xda ','xda-');
		
		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}
		
		if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
			$mobile_browser++;
		}
		
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
			$mobile_browser = 0;
		}
		
		if(self::isApple())
		{
			$mobile_browser++;
		}
		
		return $mobile_browser;
	}
	static function isApple($model = null){
		if($model){
			//format model string
			$model = strtolower($model);
			for($i=(strlen($model)-1);$i>=0;--$i){
				if($model{$i} == 'p'){
					$model{$i} = 'P';
				}
			}
			
			//check UA
			return strpos($_SERVER['HTTP_USER_AGENT'],$model) !== false;
		}
		
		//Check for any
		return (strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') !== false || strpos($_SERVER['HTTP_USER_AGENT'],'iPod') !== false || strpos($_SERVER['HTTP_USER_AGENT'],'iPad') !== false);
	}
	static function isWebApp(){
		if (self::isApple()) {
			if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),"safari")) {
				return false;
			}else{
				return true;
			}
		}
		return false;		
	}
}