<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules;
class AdultDVDEmpire extends Internal\ModuleBase implements \Net\ExternalInterfaces\ContentAPI\Interfaces\IFromURL {
	const URL_RULE = '#adultdvdempire\.com\/([0-9]+)\/#i';
	const URL = 'http://www.adultdvdempire.com';
	
	static function getFields(){
		return array('categories','featuring','cast','studio','title','description','release_date','images');
	}
	
	function toURL(){
		return self::URL.'/'.$this->id.'/';
	}
	
	function Fetch() {
		$ret = array();
		
		//Curl Fetch
		$url = $this->toURL();
		$ch = static::CH ( $url );
		$data = curl_exec ( $ch );
		
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom = \HTML\str_get_dom ( $data );
		
		try {
			$ret ['categories'] = array ();
			foreach ( $dom->find ( '#ctl00_ContentPlaceHolder_ItemViewLoader1_ctl00_BrowseBox_pnl_Genre a', null, true ) as $v ) {
				$ret ['categories'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['featuring'] = array ();
			foreach ( $dom->find ( '.Item_CastListPrimaryName', null, true ) as $v ) {
				$ret ['featuring'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['cast'] = array ();
			foreach ( $dom->find ( '.Item_CastListSecondaryName', null, true ) as $v ) {
				$v = $v->find('a',0);
				if(!$v) continue;
				$ret ['cast'] [] = $v->plaintext;
			}
		} catch ( \Exception $e ) {
		}
		
		$ret ['cast'] = array_unique($ret ['cast']);
		
		try {
			$ret ['studio'] = $dom->find ( '.Item_StudioProductionRating', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$ret ['title'] = $dom->find ( 'div.Item_Title', 0, true )->plaintext;
		} catch ( \Exception $e ) {
		}
		
		try {
			$dom->find ( '.Item_Novelty_ReturnWarning', 0, true )->innertext = '';
			$ret ['description'] = trim($dom->find ( '.Item_InfoContainer', 0, true )->plaintext);
		} catch ( \Exception $e ) {
		}
		
		if(preg_match('#Release Date:([^<]+)#',$data,$m)){
			$ret ['release_date'] = trim($m[1]);
		}
		
		if(preg_match('#http:\/\/cdn([a-z0-9]{1,2})\.dvdempire\.org\/products\/#',$data,$m)){
			$server = $m[0];
		}
		
		$idpart = substr((string)$this->id,0,2);

		$ret ['images'] = new Internal\ImageManager();
		try {
			$url = $server.$idpart.'/'.$this->id.'h.jpg';
			$ret ['images']->Add($url,array('cover','front'));
			$url = $server.$idpart.'/'.$this->id.'bh.jpg';
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