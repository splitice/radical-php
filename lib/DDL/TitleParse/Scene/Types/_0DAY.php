<?php
namespace DDL\TitleParse\Scene\Types;

class _0DAY extends Internal\SceneBase {
	protected $title;
	protected $version;
	protected $type;
	protected $os = 0;
	protected $tags = array();
	protected $game = false;
	
	const TYPE_FULL = 0;
	const TYPE_KEYGEN = 1;
	const TYPE_CRACKED = 2;
	
	const OS_WIN = 0;
	const OS_MAC = 1;
	const OS_LINUX = 2;
	
	const NATIVE_TYPE = 'app';
	
	function __construct($string){
		parent::__construct($string);
		$this->Parse();
	}
	protected function Split($string){
		return explode(static::DELIMITER,$string);
	}
	static function CleanString($str){
		$str = str_replace('.', ' ', $str);
		return trim($str);
	}
	
	function Parse(){
		if(!$this->parts){
			return;
		}
		$reverse = array_reverse($this->parts);
		if(count($reverse)>=2){
			if(strtolower($reverse[0]) == 'game'){
				unset($reverse[0]);
				$reverse = array_values($reverse);
				$this->game = true;
			}
			if((strtolower($reverse[0]) == 'keymaker' || strtolower($reverse[0]) == 'keygen' ) || strtolower($revers[1]) == 'incl'){
				unset($reverse[0],$reverse[1]);
				$reverse = array_values($reverse);
				$this->type = self::TYPE_KEYGEN;
			}elseif(strtolower($reverse[0]) == 'cracked'){
				unset($reverse[0]);
				$reverse = array_values($reverse);
				$this->type = self::TYPE_CRACKED;
			}
			if(strtolower($reverse[0]) == 'macosx'){
				unset($reverse[0]);
				$reverse = array_values($reverse);
				$this->os = self::OS_MAC;
			}
			if(substr($reverse[0],0,3) == 'Win'){
				unset($reverse[0]);
				$reverse = array_values($reverse);
				$this->os = self::OS_WIN;
			}
			if(strtolower($reverse[0]) == 'linux'){
				unset($reverse[0]);
				$reverse = array_values($reverse);
				$this->os = self::OS_LINUX;
			}
			if($reverse[0] == 'Multilingual'){
				unset($reverse[0]);
				$reverse = array_values($reverse);
				$this->multilingual = true;
			}
			$this->parts = array_reverse($reverse);
			do {
				$this->title .= ' '.$this->extractPart(0);
				$next = $this->extractPart(0,true);
			}while($next{0} != 'v');
			$this->title = ltrim($this->title);
			
			$this->version = implode('.',$this->parts);
			$this->parts = array();
		}else{
			$this->isValid(false);//2 segments? Fake
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

	static function is($str,$type_sure=false){
		if($type_sure){
			return parent::is($str);
		}
		return false;//Cant be identified!
	}
}