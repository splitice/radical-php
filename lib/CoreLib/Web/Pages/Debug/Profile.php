<?php
namespace Web\Pages\Debug;
use Web\PageHandler;
use Debug\WebGrind;

class Profile extends PageHandler\HTMLPageBase {
	protected $filename;
	
	function __construct($filename){
		$this->filename = $filename;
	}
	
	function _get($param, $default=false){
		return (isset($_GET[$param])? $_GET[$param] : $default);
	}
	
	function costCmp($a, $b){
		$a = $a['summedSelfCost'];
		$b = $b['summedSelfCost'];
	
		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? -1 : 1;
	}
	
	function GET(){
		// Make sure we have a timezone for date functions.
		if (ini_get('date.timezone') == '')
			date_default_timezone_set( WebGrind\Config::$defaultTimezone );
		
		
		switch($this->_get('op')){
			case 'function_list':
				$dataFile = $this->_get('dataFile');
				if($dataFile=='0'){
					$files = WebGrind\FileHandler::getInstance()->getTraceList();
					$dataFile = $files[0]['filename'];
				}
				$reader = WebGrind\FileHandler::getInstance()->getTraceReader($dataFile, $this->_get('costFormat', WebGrind\Config::$defaultCostformat));
				$functions = array();
				$shownTotal = 0;
				$breakdown = array('internal' => 0, 'user' => 0, 'class' => 0, 'include' => 0);
		
				for($i=0;$i<$reader->getFunctionCount();$i++) {
					$functionInfo = $reader->getFunctionInfo($i);
		
		
					if (false !== strpos($functionInfo['functionName'], 'php::')) {
						$breakdown['internal'] += $functionInfo['summedSelfCost'];
						$humanKind = 'internal';
						$kind = 'blue';
					} elseif (false !== strpos($functionInfo['functionName'], 'require_once::') ||
							false !== strpos($functionInfo['functionName'], 'require::') ||
							false !== strpos($functionInfo['functionName'], 'include_once::') ||
							false !== strpos($functionInfo['functionName'], 'include::')) {
						$breakdown['include'] += $functionInfo['summedSelfCost'];
						$humanKind = 'include';
						$kind = 'grey';
					} else {
						if (false !== strpos($functionInfo['functionName'], '->') || false !== strpos($functionInfo['functionName'], '::')) {
							$breakdown['class'] += $functionInfo['summedSelfCost'];
							$humanKind = 'class';
							$kind = 'green';
						} else {
							$breakdown['user'] += $functionInfo['summedSelfCost'];
							$humanKind = 'procedural';
							$kind = 'orange';
						}
					}
					if (!(int)$this->_get('hideInternals', 0) || strpos($functionInfo['functionName'], 'php::') === false) {
						$shownTotal += $functionInfo['summedSelfCost'];
						$functions[$i] = $functionInfo;
						$functions[$i]['nr'] = $i;
						$functions[$i]['kind'] = $kind;
						$functions[$i]['humanKind'] = $humanKind;
					}
		
				}
				usort($functions,'costCmp');
		
				$remainingCost = $shownTotal*$this->_get('showFraction');
		
				$result['functions'] = array();
				foreach($functions as $function){
		
					$remainingCost -= $function['summedSelfCost'];
		
					$result['functions'][] = $function;
					if($remainingCost<0)
						break;
				}
				$result['summedInvocationCount'] = $reader->getFunctionCount();
				$result['summedRunTime'] = $reader->formatCost($reader->getHeader('summary'), 'msec');
				$result['dataFile'] = $dataFile;
				$result['invokeUrl'] = $reader->getHeader('cmd');
				$result['runs'] = $reader->getHeader('runs');
				$result['breakdown'] = $breakdown;
				$result['mtime'] = date(WebGrind\Config::$dateFormat,filemtime(WebGrind\Config::xdebugOutputDir().$dataFile));
				echo json_encode($result);
				break;
			case 'callinfo_list':
				$reader = WebGrind\FileHandler::getInstance()->getTraceReader($this->_get('file'), $this->_get('costFormat', WebGrind\Config::$defaultCostformat));
				$functionNr = $this->_get('functionNr');
				$function = $reader->getFunctionInfo($functionNr);
					
				$result = array('calledFrom'=>array(), 'subCalls'=>array());
				$foundInvocations = 0;
				for($i=0;$i<$function['calledFromInfoCount'];$i++){
					$invo = $reader->getCalledFromInfo($functionNr, $i);
					$foundInvocations += $invo['callCount'];
					$callerInfo = $reader->getFunctionInfo($invo['functionNr']);
					$invo['file'] = $callerInfo['file'];
					$invo['callerFunctionName'] = $callerInfo['functionName'];
					$result['calledFrom'][] = $invo;
				}
				$result['calledByHost'] = ($foundInvocations<$function['invocationCount']);
		
				for($i=0;$i<$function['subCallInfoCount'];$i++){
					$invo = $reader->getSubCallInfo($functionNr, $i);
					$callInfo = $reader->getFunctionInfo($invo['functionNr']);
					$invo['file'] = $function['file']; // Sub call to $callInfo['file'] but from $function['file']
					$invo['callerFunctionName'] = $callInfo['functionName'];
					$result['subCalls'][] = $invo;
				}
				echo json_encode($result);
		
				break;
			case 'fileviewer':
				$file = $this->_get('file');
				$line = $this->_get('line');
		
				if($file && $file!=''){
					$message = '';
					if(!file_exists($file)){
						$message = $file.' does not exist.';
					} else if(!is_readable($file)){
						$message = $file.' is not readable.';
					} else if(is_dir($file)){
						$message = $file.' is a directory.';
					}
				} else {
					$message = 'No file to view';
				}
				return new WebGrind\Template('fileviewer',$this->filename);
			default:
				return new WebGrind\Template('index',$this->filename);
		}
	}
}