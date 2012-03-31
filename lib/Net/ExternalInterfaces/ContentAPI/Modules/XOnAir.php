<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules;
class XOnAir extends Internal\ModuleBase implements \Net\ExternalInterfaces\ContentAPI\Interfaces\IFromURL {
	const URL_RULE = '#xonair\.com\/detail\.cfm\?(?:.*)id=([0-9]+)#i';
	const URL = 'http://www.xonair.com';
	
	static function getFields(){
		return array('categories','cast','studio','title','description','release_date','images');
	}
	
	function toURL(){
		return self::URL.'/detail.cfm?id='.$this->id;
	}
	
	function Fetch() {
		$ret = array();
		
		//Curl Fetch
		$url = $this->toURL();
		$ch = static::CH ( $url );
		$data = curl_exec ( $ch );
		
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom = \HTML\str_get_dom ( $data );
		
		$tref = 'table[width="518"] table[width="100%"] ';
		try {
			$ret ['categories'] = array ();
			foreach ( $dom->find ( $tref.'a[href*="searchGenre.cfm"]', null, true ) as $v ) {
				$ret ['categories'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['cast'] = array ();
			foreach ( $dom->find ( $tref.'a[href*="searchPerf.cfm"]', null, true ) as $v ) {
				$ret ['cast'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['studio'] = $dom->find ( 'a[href*="searchStudio.cfm"]', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['title'] = $dom->find ( 'b[style="color:CC0000"]', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['description'] = $dom->find ( 'p', 0, true )->parent()->innertext;
			$ret ['description'] = explode('<p>',$ret ['description']);
			$ret ['description'] = trim(strip_tags($ret ['description'][0]));
		} catch ( \Exception $e ) {
		}
		
		if(preg_match('#Release Date:<\/b><\/td>(?:.+)<td>([^<]+)</td>#',$data,$m)){
			$ret ['release_date'] = $m[1];
		}
		
		$ret ['images'] = new Internal\ImageManager();
		try {
			$url = $dom->find ( 'img[src*="psimages"]', 0, true )->src;
			$url = str_replace('psimages','pdimages',$url);
			$ret ['images']->Add($url,array('cover','front'));
			$url = str_replace('.jpg','_p.jpg',$url);
			$ret ['images']->Add($url,array('cover','back'));
		} catch ( \Exception $e ) {
		}
		
		$dom->clear ();
		
		return $ret;
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