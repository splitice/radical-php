<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules;
class IMDB extends Internal\ModuleBase implements \Net\ExternalInterfaces\ContentAPI\Interfaces\IFromURL {
	const URL_RULE = '#imdb\.com\/title\/tt([0-9]+)#i';
	const URL = 'http://www.imdb.com';
	
	static function getFields(){
		return array('type','year','title','release_date','rating','genres','description','images','cast','trivia','languages','budget','running_time','aka');
	}
	
	function toURL(){
		return self::URL.'/title/tt'.$this->id;
	}
	
	function __construct($id){
		parent::__construct((int)$id);
	}
	
	const SEARCH_CACHE_TIME = 172800;
	
	/**
	 * Internal: Parse the Secondary Image Page
	 * @return Array <integer, \DDL\Bot\Resource>
	 */
	private function PARSE_SECONDARY_IMG() {
		return array();
		$url = $this->toURL() . '/mediaindex';
		$request = new \HTTP\Fetch ( $url );
		$data = $request->Execute ();
	
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom =\HTML\str_get_dom ( $data->getContent () );
	
		$ret = array ();
		foreach ( $dom->find ( '.thumb_list img', null, true ) as $img ) {
			$ret [] = self::_IMG_DECODE ( $img->src );
			if(count($ret)>5){
				break;
			}
		}
	
		$dom->clear();
	
		return $ret;
	}
	
	private function PARSE_TRIVIA(){
		$url = $this->toURL() . '/trivia';
		$request = new \HTTP\Fetch ( $url );
		$data = $request->Execute ();
	
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom =\HTML\str_get_dom ( $data->getContent () );
	
		$ret = array ();
		foreach ( $dom->find ( '.sodatext', null, true ) as $text ) {
			$ret [] = self::HTMLDecode ($text->plaintext);
			if(count($ret)>=8){
				break;//Limit
			}
		}
	
		$dom->clear();
	
		return $ret;
	}
	
	private function _fullDescription(){
		$url = $this->toURL() . '/plotsummary';
		$request = new \HTTP\Fetch ( $url );
		$data = $request->Execute ();
	
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom =\HTML\str_get_dom ( $data->getContent () );
	
		$plot = $dom->find('p.plotpar',0);
		if(!$plot){
			return null;
		}
		$a = $plot->find('i',0);
		if($a){
			$a->innertext = '';
		}
		
		$plot = static::HTMLDecode($plot->plaintext);
	
		$dom->clear();
	
		return trim($plot);
	}
	
	private function PARSE_AKA(){
		$url = $this->toURL() . '/releaseinfo';
		$request = new \HTTP\Fetch ( $url );
		$data = $request->Execute ();
	
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom =\HTML\str_get_dom ( $data->getContent () );
	
		$ret = array ();
		$link = $dom->find('a[name=akas]',0);
		if(!$link){
			$dom->clear();
			return array();
		}
		$table = $link->parent();
		$table = $table->next_sibling () ;
		if($table){
			try{
				foreach ( $table->find ( 'tr', null, true ) as $text ) {
					$ret [] = self::HTMLDecode ($text->find('td',0)->plaintext);
				}
			}catch(\Exception $ex){
				die($data->getContent ());
			}
		}
	
		$dom->clear();
	
		return $ret;
	}
	
	/**
	 * Turn an IMDB media (e.g thumbnail) URL into a full sized stored resource ID
	 * @param string $href
	 * @return String
	 */
	private static function _IMG_DECODE($href) {
		//http://ia.media-imdb.com/images/M/MV5BMjIyOTQ1MjcwNV5BMl5BanBnXkFtZTcwMzQ5NTg1NA@@._V1._CR342,0,1363,1363_SS100_.jpg
		$atpos = strpos ( $href, '._V1.' );
		$href = substr ( $href, 0, $atpos );
	
		return $href;
	}
	
	static function LookupId($imdbid) {
		$ret = array();
	
		$request = new \HTTP\Fetch ( self::getUrl($imdbid) );
		$data = $request->Execute ();
	
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom = \HTML\str_get_dom ( $data->getContent () );
	
		
	
		return $ret;
	}
	static function _SearchUpdate($hash){
		$data = array('search_hash'=>$hash,'search_date'=>\DB::toTimeStamp(time()));
		\DB::Insert('movie_search_log', $data, -1);
	}	
	function Fetch() {
		$ret = array();
		
		//Curl Fetch
		$url = $this->toURL();
		$ch = static::CH ( $url );
		$data = curl_exec ( $ch );
		
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom = \HTML\str_get_dom ( $data );
		
		//1st module
		try {
			$section = $dom->find ( 'table#title-overview-widget-layout', 0, true );	
	
			//Parse header
			try {
				$tempo = $section->find ( 'h1.header span', 0, true );
	
				//Remove ( )
				$temp = substr ( $tempo->plaintext, 1, - 1 );
	
				//Parse type
				if (substr ( $temp, 0, 10 ) == 'TV Series ') {//TV series
					$temp = substr ( $temp, 10 );
					$ret ['type'] = 'tv';
				} elseif (substr ( $temp, 0, 11 ) == 'Video Game ') {
					$temp = substr ( $temp, 11 );
					$ret ['type'] = 'game';
				} elseif (substr ( $temp, 0, 3 ) == 'TV ') {
					$temp = substr ( $temp, 3 );
					$ret ['type'] = 'movie';//tv movie!
				} elseif (substr ( $temp, 0, 6 ) == 'Video ') {
					$temp = substr ( $temp, 6 );
					$ret ['type'] = 'movie';
				} else {
					$ret ['type'] = 'movie';
				}
	
				try{
					$temp = $section->find('a[href*=/year/]',0,true)->plaintext;
					//Parse year
					if (strpos ( $temp, '-' )) { //XXXX-YYYY
						$ret ['year'] = explode ( '-', $temp );
					} else {
						$ret ['year'] = trim($temp);
					}
	
					$section->find('a[href*=/year/]',0,true)->parent()->innertext = '';
				}catch(\Exception $ex){
					$temp = trim($temp);
					if(is_numeric($temp)){
						$ret['year'] = $temp;
					}else{
						$temp = trim($tempo->find('span',0,true)->plaintext,"\r\n ()");
						if(substr($temp,5)=='Video'){
							$ret['year'] = (int)substr($temp,6);
						}
					}
				}
	
			} catch ( \Exception $e ) {
				try {
					$tempo = $section->find ( 'h2.tv_header span', 0, true );
	
					//Remove ( )
					$temp = substr ( $tempo->plaintext, 1, - 1 );
	
					//Parse type
					if (substr ( $temp, 0, 9 ) == 'TV series') {//TV series
						$temp = substr ( $temp, 10 );
						$ret ['type'] = 'tv';
						$ret['year'] = substr($temp,4);
						if(!is_numeric($ret['year'])){
							$ret['year'] = null;
						}
					}
				}catch ( \Exception $e ) {
	
				}
			}
	
			//Parse title
			try {
				foreach($section->find ( 'h1.header', 0, true )->find('span') as $s){
					//echo $s->innertext,"\r\n";
					$s->innertext = '';
				}
				$ret ['title'] = trim(self::HTMLDecode ( trim ( $section->find ( 'h1.header', 0, true )->plaintext ) ));
				$ret ['title'] = str_replace(array("\n","\r\n",'  '),'',$ret ['title']);
			} catch ( \Exception $e ) {
			}
	
			//Parse release date
			try {
				$ret ['release_date'] = str_replace ( array ("\r\n", "\n" ), ' ', self::HTMLDecode ( $section->find ( 'a[title="See all release dates"]', 0, true )->plaintext ) );
				$m = array ();
				if (preg_match ( '#(.+) \((.+)\)#', $ret ['release_date'], $m )) {
					$ret ['release_date'] = array ();
					$ret ['release_date'] [$m [2]] = self::HTMLDecode ( $m [1] );
					//TODO: aditional dates
				}
			} catch ( \Exception $e ) {
			}
	
			//Parse rating
			try {
				$r = trim ( $section->find ( 'span[itemprop="ratingValue"]', 0, true )->plaintext );
				$r = array($r,10);
				$v = intval ( str_replace(',','',$section->find ( 'span[itemprop="ratingCount"]', 0, true )->plaintext ));
	
				$ret ['rating'] = array ('rating' => $r [0], 'total' => $r [1], 'numvotes' => $v );
			} catch ( \Exception $e ) {
			}
	
			//Parse desc
			try {
				foreach ( $dom->find ( 'p[itemprop="description"]', null, true ) as $s ) {
					if($s->find('a[href="plotsummary"]',0)){
						$ret ['description'] = $this->_fullDescription();
					}else{
						$s = trim ( $s->plaintext );
						if ($s) {
							$ret ['description'] = self::HTMLDecode ( trim ( $s ) );
						}
					}
				}
			} catch ( \Exception $e ) {
			}
	
			//Parse primary image
			$ret ['images'] = new Internal\ImageManager();
			try {
				$url = self::_IMG_DECODE ( $section->find ( 'td#img_primary', 0, true )->find ( 'img', 0, true )->src );
				$ret ['images']->Add($url,array('poster'));
			} catch ( \Exception $e ) {
			}
	
			/*try {
				$ret ['image'] ['secondary'] = array ();
				if ($dom->find ( 'a[href=mediaindex]', 0, true )) {
					$ret ['image'] ['secondary'] = $this->PARSE_SECONDARY_IMG ();
				}
			} catch ( \Exception $e ) {
			}*/
		} catch ( \Exception $e ) {
		}
	
	
	
		//Parse cast table & 2nd module
		try {
			$ret ['cast'] = array ();
			foreach ( $dom->find ( 'table.cast_list tr[class]', null, true ) as $g ) {
				try {
					//Parse
					$name = self::HTMLDecode ( trim ( $g->find ( 'td.name', 0, true )->plaintext ) );
					$character = self::HTMLDecode ( trim ( $g->find ( 'td.character', 0, true )->plaintext ) );
					if(!$character){
						$character = null;
					}
					
					//As
					$as = null;
					$as_regex = '# \(as ([^\)]+)\)#';
					if(preg_match($as_regex,$character,$m)){
						$character = trim(preg_replace($as_regex,'',$character));
						$as = $m[1];
					}
	
					$ret ['cast'] [] = new Structs\CastMember($name,$character,$as);
				} catch ( \Exception $e ) {
				}
			}
		} catch ( \Exception $e ) {
		}
	
		try {
			//Genre
			$ret ['genres'] = array ();
			try {
				if(!isset($section)){
					$section = $dom;
				}
				foreach ( $section->find ( 'a[href*="/genre/"]', null, true ) as $s ) {
					$s = trim ( $s->plaintext );
					if ($s) {
						$ret ['genres'] [] = self::HTMLDecode ( trim ( $s ) );
					}
				}
			} catch ( \Exception $e ) {
			}
		}catch ( \Exception $e ) {
		}
	
		try {
			$ret ['trivia'] = array ();
			$t = $this->PARSE_TRIVIA();
			$ret ['trivia'] = $t;
		} catch ( \Exception $e ) {
		}
	
		//Language
		$ret ['languages'] = array ();
		try {
			foreach ( $dom->find ( 'a[href*="/language/"]', null, true ) as $s ) {
				$s = trim ( $s->plaintext );
				if ($s) {
					$ret ['languages'] [] = self::HTMLDecode ( trim ( $s ) );
				}
			}
		} catch ( \Exception $e ) {
		}
	
		$m = array();
		if(preg_match('#Budget:<\/h4>([^\(^\<]+)#',$data,$m)){
			$d = str_replace(array('$',',','.'),'',trim($m[1]));
			$ret ['budget'] = $d;
		}
	
		if(preg_match('#Runtime:<\/h4>([^\<]+)#',$data,$m)){
			$d =$m[1];
			if($pos = strpos($d,':')){
				$d = substr($d,$pos+1);
			}
			$ret['running_time'] = \Basic\DateTime\Timespan::fromHuman(trim($d));
		}
	
		if(!isset($ret['running_time'])){
			try {
				$temp = $section->find ( 'div.infobar', 0, true );
				foreach($temp->find('span') as $s){
					$s->innertext = '';
				}
				foreach($temp->find('a') as $s){
					$s->innertext = '';
				}
				if(preg_match('#([0-9]+) min#',(string)$temp,$m)){
					$ret['running_time'] = \Basic\DateTime\Timespan::fromHuman($m[0]);
				}
			}catch(\Exception $ex){
	
			}
		}
	
		$dom->clear();
	
		$ret['aka'] = self::PARSE_AKA();
		
		$dom->clear ();
		
		return $ret;
	}
	
	function getTitle(){
		return $this->Parse('title');
	}
	function getYear(){
		return $this->Parse('year');
	}
	function getDescription(){
		return $this->Parse('description');
	}
	function getGenres(){
		return $this->Parse('categories');
	}
	function getLanguages(){
		return $this->Parse('languages');
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
/*	function getReleaseDate(){
		return $this->Parse('release_date');
	}*/
}
?>