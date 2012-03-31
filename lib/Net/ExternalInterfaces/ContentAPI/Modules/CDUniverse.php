<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules;
use \Net\ExternalInterfaces\ContentAPI\Interfaces;

class CDUniverse extends Internal\ModuleBase implements Interfaces\IFromURL {
	const URL_RULE = '#cduniverse\.com\/productinfo\.asp\?(?:.*)pid=([0-9]+)#i';
	const URL = 'http://www.cduniverse.com';
	
	static function getFields(){
		return array('director','categories','cast','studio','title','description','release_date','images');
	}
	
	static function imgCurl($ch,$url){
		$url = static::URL . html_entity_decode($url);
		$ch_new = curl_copy_handle($ch);
		curl_setopt($ch, CURLOPT_URL, $url);
		return new Internal\CDUImageFetch($ch_new);
	}
	
	function toURL(){
		return self::URL.'/productinfo.asp?pid='.$this->id;
	}
	
	static function CH($url=null) {
		$ch = parent::CH($url);
		curl_setopt ( $ch, CURLOPT_COOKIE, 'IAmAnAdult=yes' );//Allow 18+ material
		return $ch;
	}
	function Fetch() {
		$ret = array();
		
		//Curl Fetch
		$ch = self::CH ( $this->toURL() );
		$data = curl_exec ( $ch );

		\HTML\Simple_HTML_DOM::LoadS ();
		$dom = \HTML\str_get_dom ( $data );
		
		try {
			$ret ['director'] = $dom->find ( 'a[href*="HT_Search=xdirector"]', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['categories'] = array ();
			foreach ( $dom->find ( 'table#pagecenterarea a.categorylink[href*="browsecat.asp"]', null, true ) as $v ) {
				$ret ['categories'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['cast'] = array ();
			foreach ( $dom->find ( 'table#pagecenterarea a.categorylink[href*="HT_Search=xstar"]', null, true ) as $v ) {
				$ret ['cast'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['studio'] = $dom->find ( 'a[id=studiolink]', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['title'] = $dom->find ( 'title', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['description'] = strip_tags($dom->find ( 'div[style="margin-top:10px;"]', 0, true )->plaintext);
		} catch ( \Exception $e ) {
		}
		
		//Content table
		try {
			foreach ( $dom->find ( 'div[id=cb_Product Detail] .content table tr', null, true ) as $r ) {
				$k = trim ( $r->find ( 'td[class=info]', 0, true )->plaintext );
				$v = trim ( $r->find ( 'td', 1, true )->plaintext );
				switch ($k) {
					case 'DVD Encoding' :
						$ret ['dvd_encoding'] = $v;
						break;
					case 'Discs' :
						$ret ['no_disks'] = $v;
						break;
					case 'Release Date' :
						$m = array ();
						if (preg_match ( '#release date (.+)#', $v, $m )) {
							$v = $m [1];
						}
						$ret ['release_date'] = $v;
						break;
				}
			}
		} catch ( \Exception $e ) {
		}
		
		$ret ['images'] = new Internal\ImageManager();
		try {
			$url = $dom->find ( 'table#igcovera1 a.coverart[href*="image=back"]', 0, true )->href;
			$ret ['images']->Add(self::imgCurl($ch,$url),array('cover','back'));
		} catch ( \Exception $e ) {
		}
		
		try {
			$url = $dom->find ( 'table#igcovera1 a.coverart[href*="image=front"]', 0, true )->href;
			$ret ['images']->Add(self::imgCurl($ch,$url),array('cover','front'));
		} catch ( \Exception $e ) {
		}
		
		$dom->clear ();
		
		return $ret;
	}
	function Parse($want = null,$export = true){
		if($this->_cache !== null){
			if($want){
				$ret = isset($this->_cache[$want])?$this->_cache[$want]:null;
				if($export && is_object($ret) && ($ret instanceof Interfaces\IExportable)){
					$ret = $ret->toExport();
				}
				return $ret;
			}
		}else{
			$this->_cache = $this->Fetch();
			if($want)
				return $this->Parse($want,$export);
		}
	}
	
	function getTitle(){
		return $this->Parse('title');
	}
	function getDescription(){
		return $this->Parse('description');
	}
	function getCategories(){
		return $this->Parse('categories');
	}
	function getImages($tag=null){
		if($tag == null){
			return $this->Parse('images');
		}
		if($i = $this->Parse('images',false)){
			return $i->getByTag($tag);
		}
		return array();
	}
	function getCast(){
		return $this->Parse('cast');
	}
	function getStudio(){
		return $this->Parse('studio');
	}
	function getReleaseDate(){
		return $this->Parse('release_date');
	}
}
?>