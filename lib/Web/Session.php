<?php
namespace Web;

use Web\Session\Handler\Internal\ISessionHandler;

class Session extends \Core\Object {
	static $__dependencies = array(
			'interface.Web.Session.Handler.Internal.ISessionHandler',
			'interface.Web.Session.Extra.Interfaces.ISessionExtra',
		);
	
	const DEFAULT_IP = '::1';
	
	public static $data;
	
	static function IP(){
		if(\Server::isCLI()){
			return new \Net\IP(static::DEFAULT_IP);
		}
		if(isset($_SERVER['HTTP_X_REAL_IP'])){
			return new \Net\IP($_SERVER['HTTP_X_REAL_IP']);
		}
		if(isset($_SERVER['REMOTE_ADDR'])){
			return new \Net\IP($_SERVER['REMOTE_ADDR']);
		}
		return new \Net\IP(static::DEFAULT_IP);
	}
	
	static function Init(ISessionHandler $handler = null){
		if($handler) {
			static::$data = $handler;
		}	
	}
	/*static function LoadsJS($file){
		$ver = \PageHandler\JS\Combine::jsVersion();
		if(!self::$page){
			$hash = \DB::BIN(md5($_SERVER['REQUEST_URI']));
			$res = \DB::Q('SELECT page_id FROM page WHERE page_urlhash='.\DB::E($hash));
			self::$page = \DB::Fetch($res,\DB::FETCH_FIRST);
			if(!self::$page){
				\DB::Insert('page',array('page_urlhash'=>$hash));
				self::$page = \DB::InsertId();
			}
			\DB::Insert('page_load',array('session_id'=>self::$id,'page_id'=>self::$page));
		}
		
		$res = \DB::Q('SELECT javascript_id FROM javascript WHERE javascript_version='.\DB::E($ver).' AND javascript_module='.\DB::E($file));
		$js_id = (int)\DB::Fetch($res,\DB::FETCH_FIRST);
		if(!$js_id){
			$js_id = \PageHandler\JS\Individual::Insert($file);
		}
		
		\DB::Insert('page_javascript',array('page_id'=>self::$page,'javascript_id'=>$js_id));
		
		return $js_id;
	}*/
	
}