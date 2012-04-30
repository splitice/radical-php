<?php
namespace DDL\TitleParse\Scene\Types\Internal;

class TVBase extends SceneBase {
	const DELIMITER = '.';
	const NATIVE_TYPE = 'tv';
	
	protected $show;
	protected $encoding;
	protected $source;
	protected $title;
	
	protected $ref_type;
	protected $season;
	protected $episode;
	protected $date;
	
	function __construct($string) {
		parent::__construct ( $string );
		$this->Parse ();
	}
	function isTag(){
		switch(strtolower($s)){
			case 'dvdrip':
			case 'bdrip':
			case 'bluray':
			case 'bd':
			case 'dvdr':
			case 'dvd5':
			case 'dvd9':
			case 'cam':
			case 'ts':
			case 'r5':
			case 'tc':
			case 'dvdscr':
			case 'scr':
			case '1080p':
			case '720p':
				return true;
				break;
		}
		return false;
	}
	
	static function CleanString($str) {
		$str = implode ( ' ', $str );
		return trim ( $str );
	}
	
	static function _isEpisodeRef($str) {
		$str = strtoupper ( $str );
		if (preg_match ( '#E([0-9]+)#', $str )) {
			return true;
		} elseif (preg_match ( '#([0-9]+)x([0-9]+)#', $str )) {
			return true;
		}
		return false;
	}
	static function _isSeasonRef($str) {
		if ($str {0} == 'S') {
			return true;
		}
		return false;
	}
	static function _isDateRef($part1, $part2, $part3) {
		if (is_numeric ( $part1 ) && is_numeric ( $part2 ) && is_numeric ( $part3 )) {
			return true;
		}
		return false;
	}
	
	function ParseEpisode($ref_pos) {
		$str = $this->extractPart($ref_pos);
		$season = $episode = null;
		$str = strtoupper ( $str );
		$m = array ();
		if (preg_match ( '#S([0-9]+)E([0-9]+)#', $str, $m ) || preg_match ( '#([0-9]+)x([0-9]+)#', $str, $m )) {
			$season = ( int ) $m [1];
			$episode = ( int ) $m [2];
		} elseif (preg_match ( '#E([0-9]+)#', $str, $m )) {
			$episode = (int)$m[1];
		} else{
			return;
		}
		$str = substr ( $str, strlen ( $m [0] ) );
		if ($str) {
			$episode = array ($episode );
			if ($str {0} == '-') {
				if($str{1}=='E'){
					$episode = range ( $episode [0], substr ( $str, 2 ) );
				}else{
					$episode = range ( $episode [0], substr ( $str, 1 ) );
				}
			} elseif ($str {0} == 'E') {
				while ( $str {0} == 'E' ) {
					if (preg_match ( '#^E([0-9]+)#', $str, $m )) {
						$episode [] = ( int ) $m [1];
						$str = substr ( $str, strlen ( $m [0] ) );
					}
				}
			}
		}
		
		$this->season = $season;
		if(is_array($episode) && count($episode) == 1){
			$episode = $episode[0];
		}
		$this->episode = $episode;
	}
	function ParseSeason($ref_pos) {
		$str = $this->extractPart($ref_pos);
		if (preg_match ( '#S([0-9]+)#', $str, $m )) {
			$season = (int)$m[1];
			$str = substr ( $str, strlen ( $m [0] ) );
			if ($str) {
				$season = array ($season );
				if ($str {0} == '-') {
					if($str{1}=='S'){
						$season = range ( $season [0], (int)substr ( $str, 2 ) );
					}else{
						$season = range ( $season [0], (int)substr ( $str, 1 ) );
					}
				} elseif ($str {0} == 'S') {
					while ( $str {0} == 'S' ) {
						if (preg_match ( '#^S([0-9]+)#', $str, $m )) {
							$season [] = ( int ) $m [1];
						}
					}
				}
			}
			$this->season = $season;
		}
	}
	function ParseDate($ref_pos) {
		$date = array();
		$date[] = $this->extractPart($ref_pos-1);
		$date[] = $this->extractPart($ref_pos-1);
		$date[] = $this->extractPart($ref_pos-1);
		$date = array_reverse($date);//it is always reversed
		if(strlen($date[2]) == 2){
			$date[2] = '20'.$date[2];
		}
		$this->date =  implode('/',$date);
	}
	
	function Parse() {
		$this->encoding = $this->extractPart ( - 1 );
		$this->source = $this->extractPart ( - 1 );
		
		//Most common placement
		if(strtoupper($this->extractPart ( - 1, true )) == 'XXX'){
			$this->setValid(false);
			return;
		}
		
		$ref_pos = null;
		foreach ( $this->parts as $k => $v ) {
			if(!$k){
				continue;
			}
			if (self::_isEpisodeRef ( $v )) {
				$this->ref_type = 'episode';
				$ref_pos = $k;
				break;
			} elseif (self::_isSeasonRef ( $v )) {
				$this->ref_type = 'season';
				$ref_pos = $k;
				break;
			} elseif (isset ( $this->parts [$k + 1] ) && self::_isDateRef ( $this->parts [$k-1], $this->parts [$k], $this->parts [$k + 1] )) {
				$this->ref_type = 'date';
				$ref_pos = $k;
				break;
			}
		}
		
		if ($ref_pos === null) {
			$this->show = self::CleanString ( $this->parts );
			$this->parts = array ();
		} else {
			switch ($this->ref_type) {
				case 'episode' :
					$this->ParseEpisode ($ref_pos);
					break;
				case 'season' :
					$this->ParseSeason ($ref_pos);
					break;
				case 'date' :
					$this->ParseDate ($ref_pos);
					$ref_pos--;
					break;
			}
			
			//Look for left over XXX tag
			foreach($this->parts as $k=>$v){
				if($v == 'XXX'){
					if(!isset($this->parts[$k+1]) || $this->isTag($this->parts[$k+1])){
						$this->setValid(false);
						$this->parts = array();
						return;
					}
				}
			}
			
			$this->show = implode(' ',array_slice($this->parts, 0, $ref_pos));
			$this->parts = array_slice($this->parts,$ref_pos);
			if($this->parts){
				$this->title = implode(' ',$this->parts);
				$this->parts = array();
			}
		}
	}
	/**
	 * @return the $show
	 */
	public function getShow() {
		return $this->show;
	}

	/**
	 * @return the $encoding
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * @return the $source
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @return the $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return the $ref_type
	 */
	public function getRefType() {
		return $this->ref_type;
	}
	
	
	public function isRef($v){
		return ($this->ref_type==$v);
	}

	/**
	 * @return the $season
	 */
	public function getSeason() {
		return $this->season;
	}

	/**
	 * @return the $episode
	 */
	public function getEpisode() {
		return $this->episode;
	}

	/**
	 * @return the $date
	 */
	public function getDate() {
		return $this->date;
	}
}