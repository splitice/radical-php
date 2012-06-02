<? 
namespace Net\ExternalInterfaces;
    class rTorrent extends rTorrent\_CORE {
        private $watch_dir;
        static $hashes = array();
        
        var $host;
        
        function __construct($host){
            $this->host = $host;
        }
        
        function Ping(){
        	if(count($this->Post('system.listMethods', array()))){
        		return true;
        	}
        	return false;
        }
        
        function listTorrents(){
        	global $_CONFIG;
        	$ret = array();
        	foreach(\Folder::ListDir($_CONFIG['torrent']['session_dir'].'*.torrent') as $f){
    			$hash = basename($f,'.torrent');
        		
        		if($hash){
        			$ret[$f] = new rTorrent\Torrent($hash,$this->host);
        		}
        	}
        	return $ret;
        }
        
        static function CreateInstance(){
        	global $_CONFIG;
        	$r = new rTorrent($_CONFIG['torrent']['RPC_URL']);
        	if(!$r->Ping()){
        		throw new rTorrent\ConnectionException('The connection to "'.$_CONFIG['torrent']['RPC_URL'].'" failed!');
        	}
        	return $r;
        }
        
        function getTorrent($hash){
        	return new rTorrent\Torrent($hash,$this->host);
        }
        
    }
?>
