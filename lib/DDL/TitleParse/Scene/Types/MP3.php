<?php
namespace DDL\TitleParse\Scene\Types;

class MP3 extends Internal\SceneBase {
	const DELIMITER = '-';
	
	protected $artist;
	protected $album;
	protected $year;
	protected $type;
	protected $date;
	
	const NATIVE_TYPE = 'music';
	
	function __construct($string){
		parent::__construct($string);
		$this->Parse();
	}
	protected function Split($string){
		//$string = str_replace('_','.',$string);
		//Not MP3
		return explode(static::DELIMITER,$string);
	}
	static function CleanString($str){
		$str = str_replace('_', ' ', $str);
		return trim($str);
	}
	
	function Parse(){
		if(!$this->parts){
			return;
		}
		$this->artist = self::CleanString($this->extractPart());
		if($this->artist === null){
			$this->isValid(false);
			return false;
		}
		
		$this->album = self::CleanString($this->extractPart());
		if($this->album === null){
			$this->isValid(false);
			return false;
		}
		
		$this->year = $this->extractPart(-1);
		if($this->year === null){
			$this->isValid(false);
			return false;
		}
		
		if(isset($this->parts[0])){
			if($this->parts[0]{0} == '('){
				$this->extractPart(0);//Disreguards
			}
			
			foreach($this->parts as $nk=>$next){
				switch(strtoupper($next)){
					case 'WEB':
					case 'VINYL':
					case 'VLS':
					case '2CD':
					case '3CD':
					case '4CD':
					case '5CD':
					case '6CD':
					case '7CD':
					case '8CD':
					case '9CD':
					case '10CD':
					case '11CD':
					case '12CD':
					case 'BOOTLEG':
					case 'SAT':
					case 'DAB':
					case 'CABLE':
						$this->type = $next;
						unset($this->parts[$nk]);
						$this->parts = array_values($this->parts);
						break(2);
				}
			}
			
			if(count($this->parts) >= 2 && is_numeric($this->parts[0]) && is_numeric($this->parts[1])){
				$this->date = $this->extractPart().'/'.$this->extractPart();
			}
		}else{
			$this->isValid(false);
		}
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

	/**
	 * @return the $year
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return the $date
	 */
	public function getDate() {
		return $this->date;
	}

	
}