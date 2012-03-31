<?php
namespace Net\ExternalInterfaces;
class DeCAPTCHA {
	private static function Status($e){
		
	}
	static function Solve($file_data) {

		require_once (__DIR__.'/DeCAPTCHA/api/ccproto_client.php');
		
		global $_CONFIG;
		
		$ccp = new \ccproto ();
		$ccp->init ();
		
		
		static::Status ("Logging in...") ;
		if ($ccp->login ( $_CONFIG['decaptcha']['host'], $_CONFIG['decaptcha']['port'], $_CONFIG['decaptcha']['user'], $_CONFIG['decaptcha']['pass'] ) < 0) {
			throw new DeCAPTCHA\CaptchaError ("login() FAILED\n") ;
		}
		
		$system_load = 0;
		if ($ccp->system_load ( $system_load ) != ccERR_OK) {
			throw new DeCAPTCHA\CaptchaError ("system_load() FAILED\n") ;
		}
		static::Status ("System load=" . $system_load . " perc\n") ;
		
		$balance = 0;
		if ($ccp->balance ( $balance ) != ccERR_OK) {
			throw new DeCAPTCHA\CaptchaError ("balance() FAILED\n") ;
		}
		static::Status ("Balance=" . $balance . "\n") ;
		
		$major_id = 0;
		$minor_id = 0;
		for($i = 0; $i < 3; $i ++) {
			$text = '';
			static::Status ("sending a picture...") ;
			
			$pict_to = ptoDEFAULT;
			$pict_type = ptUNSPECIFIED;
			
			$res = $ccp->picture2 ( $file_data, $pict_to, $pict_type, $text, $major_id, $minor_id );
			switch ($res) {
				// most common return codes
				case ccERR_OK :
					return $text;
				case ccERR_BALANCE :
					throw new DeCAPTCHA\CaptchaError ("not enough funds to process a picture, balance is depleted\r\n") ;
				case ccERR_TIMEOUT :
					throw new DeCAPTCHA\CaptchaError ("picture has been timed out on server (payment not taken)\r\n") ;
				case ccERR_OVERLOAD :
					throw new DeCAPTCHA\CaptchaError ("temporarily server-side error. server's overloaded, wait a little before sending a new picture\r\n") ;
			
				// local errors
				case ccERR_STATUS :
					throw new DeCAPTCHA\CaptchaError ("local error. either ccproto_init() or ccproto_login() has not been successfully called prior to ccproto_picture() need ccproto_init() and ccproto_login() to be called\r\n") ;
				
				// network errors
				case ccERR_NET_ERROR :
					throw new DeCAPTCHA\CaptchaError ("network troubles, better to call ccproto_login() again\r\n") ;
				
				// server-side errors
				case ccERR_TEXT_SIZE :
					throw new DeCAPTCHA\CaptchaError ("size of the text returned is too big\r\n") ;
				case ccERR_GENERAL :
					throw new DeCAPTCHA\CaptchaError ("server-side error, better to call ccproto_login() again\r\n") ;
				case ccERR_UNKNOWN :
					throw new DeCAPTCHA\CaptchaError ("unknown error, better to call ccproto_login() again\r\n") ;
				
				default :
					// any other known errors?
					throw new DeCAPTCHA\CaptchaError ("unknown error (default)\r\n") ;
					break;
			}
		}
	}
}