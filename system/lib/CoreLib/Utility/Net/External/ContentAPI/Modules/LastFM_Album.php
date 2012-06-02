<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules;
use \Net\ExternalInterfaces\ContentAPI\Interfaces;

class LastFM_Album extends Internal\ModuleBase implements Interfaces\IFromURL {
	//http://www.last.fm/music/Cher/Believe
	const URL_RULE = '#last\.fm\/music\/([^\/]+)\/([^\/]+)#i';
	const URL = 'http://www.last.fm';
	
	static function getFields(){
		return array('artist','album','release_date','images','categories','tracks','description');
	}
	
	static function fromURL($url){
		if(static::RecogniseURL($url)){
			$m = array();
			if(preg_match(static::URL_RULE, $url, $m)){
				$a = new LastFM\AlbumReference($m[1], $m[2]);
				return static::fromID($a);
			}
		}
	}
	
	static function fromID($id){
		if(!($id instanceof LastFM\AlbumReference)){
			$args = func_get_args();
			if(count($args) == 2){
				$id = new LastFM\AlbumReference($args[0], $args[1]);
			}else{
				throw new \InvalidArgumentException('Must have 2 arguments or one instnace of LastFM\\AlbumReference');
			}
		}
		return parent::fromID($id);
	}
	
	function toURL(){
		return self::URL.'/music/'.$this->toURL();
	}
	
	function Fetch() {
		$ret = array();
		
		//Get Album
		$api = new LastFM\API();
		$album = $api->albumGetInfo($this->id->getArtist(), $this->id->getAlbum());
		
		//Details
		$ret['artist'] = (string)$album->artist;
		$ret['album'] = (string)$album->name;
		$ret['release_date'] = trim((string)$album->releasedate);
		
		//Get Image
		$image = null;
		foreach($album->image as $img){
			$image = (string)$img;
		}
		$ret ['images'] = new Internal\ImageManager();
		if($image){
			$ret ['images']->Add($image,array('cover','front','poster'));
		}
		
		//Get categories
		$ret ['categories'] = array();
		foreach($album->toptags->tag as $t){
			$name = (string)$t->name;
			if($name != 'albums i own'){
				$ret ['categories'][] = static::HTMLDecode($name);
			}
		}
		
		//Get tracks
		$ret ['tracks'] = array();
		foreach($album->tracks->track as $track){
			$ret ['tracks'][] = static::HTMLDecode((string)$track->name);
		}
		
		//Description
		if(isset($album->wiki) && isset($album->wiki->summary)){
			$ret['description'] = static::HTMLDecode(trim((string)$album->wiki->summary));
		}
		
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
	
	function getArtist(){
		return $this->Parse('artist');
	}
	function getAlbum(){
		return $this->Parse('album');
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
	function getReleaseDate(){
		return $this->Parse('release_date');
	}
	function getTracks(){
		return $this->Parse('tracks');
	}
}
?>