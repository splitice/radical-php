<?php
namespace Net\ExternalInterfaces\RSS;

class Document {
	private $title;
	private $link;
	private $description;
	
	private $file;
	function __construct($file) {
		$this->file = fopen ( $file, 'r' );
		if (! $this->file) {
			throw new \Exception ( 'Could not open RSS feed: ' . $file );
		}
	}
	
	private $tempData;
	private function _characterData($parser, $data) {
		$titleKey = "^RSS^CHANNEL^TITLE";
		$linkKey = "^RSS^CHANNEL^LINK";
		$descKey = "^RSS^CHANNEL^DESCRIPTION";
		if ($this->curTag == $titleKey) {
			$this->title = $data;
		} elseif ($this->curTag == $linkKey) {
			$this->link = $data;
		} elseif ($this->curTag == $descKey) {
			$this->description = $data;
		}
		
		// now get the items 
		$itemTitleKey = "^RSS^CHANNEL^ITEM^TITLE";
		$itemLinkKey = "^RSS^CHANNEL^ITEM^LINK";
		$itemDescKey = "^RSS^CHANNEL^ITEM^DESCRIPTION";
		
		if(!isset($this->tempData->itemCount)){
			$this->tempData->itemCount = 0;
		}
		$itemCount = $this->tempData->itemCount;
		
		if(!isset($this->tempData->arItems [$itemCount])){// make new Item
			$this->tempData->arItems [$itemCount] = new Item ($this);
		}
		
		if ($this->curTag == $itemTitleKey) {						
			// set new item object's properties    
			$this->tempData->arItems [$itemCount]->title .= $data;
		} elseif ($this->curTag == $itemLinkKey) {
			$this->tempData->arItems [$itemCount]->link .= $data;
		} elseif ($this->curTag == $itemDescKey) {
			$this->tempData->arItems [$itemCount]->description .= $data;
			// increment item counter
			$this->tempData->itemCount ++;
		}
	}
	
	private $curTag = '';
	private function _xmlParser() {
		//Setup tempData
		$this->tempData = new \stdClass();
		$this->tempData->arItems = array();
		
		$curTag = &$this->curTag;
		
		// main loop
		$xml_parser = xml_parser_create ();
		xml_set_element_handler ( $xml_parser, function ($parser, $name, $attrs) use(&$curTag) {
			$curTag .= "^${name}";
		}, function ($parser, $name) use(&$curTag) {
			$curTag = substr ( $curTag, 0, strrpos ( $curTag, '^' ) );
		} );
		xml_set_character_data_handler ( $xml_parser, array($this,"_characterData") );
		
		return $xml_parser;
	}
	
	function Parse() {
		$xml_parser = $this->_xmlParser ();

		while ( $data = fread ( $this->file, 4096 ) ) {
			if (! xml_parse ( $xml_parser, $data, feof ( $this->file ) )) {
				throw new \Exception ( sprintf ( "XML error: %s at line %d", xml_error_string ( xml_get_error_code ( $xml_parser ) ), xml_get_current_line_number ( $xml_parser ) ) );
			}
		}
		xml_parser_free ( $xml_parser );
	
		$items = $this->tempData->arItems;
		
		foreach($items as $k=>$v){
			if($v->isEmpty()){
				unset($items[$k]);
			}
		}
		
		unset($this->tempData);
		
		return array_values($items);
	}
}