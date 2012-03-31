<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules\LastFM;

class AlbumReference {
	private $artist;
	private $album;
	
	function __construct($artist,$album){
		$this->artist = $artist;
		$this->album = $album;
	}
	
	/**
	 * @return the $artist
	 */
	public function getArtist() {
		return $this->artist;
	}

	/**
	 * @return the $album
	 */
	public function getAlbum() {
		return $this->album;
	}
	
	function toURL(){
		return $this->artist.'/'.$this->album;
	}
}