<?php
namespace File\Format;

                   
	class DLC {

		// >>>>>>>>> PLEASE EDIT - START !!!

		// The given ID by the JD-Dev-Team
		const dlc_content_generator_id 		= 'JDOWNLOADER.ORG';
		// Name of your project
		const dlc_content_generator_name 	= 'JDownloader.org';
		// URL of your Project
		const dlc_content_generator_url 	= 'http://www.nexusddl.com';
		// Cache file for save keys - you need a absolute path and a file permission 777
		const dlc_cache_keys_filename		= '/tmp/dlcapicache.txt';

        // >>>>>>>>> PLEASE EDIT - END !!!

		// DO NOT EDIT!!!

		const dlc_api_version 				= '1.0';
		const dlc_key_pair_expires_after 	= 3600;
		const dlccrypt_mainservices 		= 'http://service.jdownloader.org/dlcrypt/service.php';
		const dlccrypt_services_mirror_1 	= false;
		const dlccrypt_services_mirror_2 	= false;
		const dlccrypt_services_mirror_3 	= false;
		const dlccrypt_services_mirror_4 	= false;
		const ccf_key_10                    = '5F679C00548737E120E6518A981BD0BA11AF5C719E97502983AD6AA38ED721C3';
		const ccf_key_08                    = '171BF8E34C3D0C0C2693FDD2B080423A5B98F4D028A0AF4D82A385D837A8F95F';
		const ccf_key_07                    = '026900E977C6402442B661329CFE62D6ED21BDEB0CD6321318A8EDC7BC5A6C86';
		const ccf_iv_10                     = 'E3D153AD609EF7358D66684180C7331A';
		const ccf_iv_08                     = '9FE95FFF7CA4FC0FCEF25E4F7444AE67';
		const ccf_iv_07                     = '8CE1173EBAD76E08584B94573926231E';
		const ccf_id_10                     = '1.0';
		const ccf_id_08                     = '0.8';
		const ccf_id_07                     = '0.7';
		const rsdf_key                      = '8C35192D964DC3182C6F84F3252239EB4A320D2500000000';
		const rsdf_iv                       = 'a3d5a33cb95ac1f5cbdb1ad25cb0a7aa';

		// DO NOT EDIT!!!

		protected $intCountErrors			= 0;
		protected $arrErrorMessages			= array();

		protected $arrModel					= array();
		protected $intPackageId 			= NULL;

		// Constructor
		function __construct() {
     
            $this->resetDataModel();
		}

		// Destructor
		function __destruct() {
		}

		// Add error
	    protected function addError($strMessage) {
	        $this->intCountErrors++;
	        $this->arrErrorMessages[] = strip_tags($strMessage);
	        return false;
	    }

		// Check if errors exists
	    public function isError() {
	        return ($this->intCountErrors) ? true : false;
	    }

		// Show errors
	    public function showError() {
	        $strResult = '';
	        if ($this->arrErrorMessages) {
	            if (count($this->arrErrorMessages) > 1) {
	                $strResult.= '<ul>';
	                foreach ($this->arrErrorMessages as $strMessage) {
	                    $strResult.= '<li>'.$strMessage.'</li>';
	                }
	                $strResult.= '</ul>';
	            } else {
	                $strResult = $this->arrErrorMessages[0];
	            }
	        }
	        return $strResult;
	    }

		public function getDataModel() {
			return $this->arrModel;
		}

		// Reset a data model
		public function resetDataModel() {
            $this->arrModel = array();
            $intPackageId = NULL;
        }

		// Add a new data model
		public function createDataModel($strUploaderName='unknown') {
			$intNewModelId = count($this->arrModel);
			$this->arrModel[$intNewModelId]['uploader'] = $strUploaderName;
			$this->arrModel[$intNewModelId]['packages'] = array();
			return $intNewModelId;
		}

		// Add a file package to a data model
		public function addFilePackage($intModelId,
									   $strName='package',
									   $strPasswords=array(),
									   $strComment='no comment',
									   $strCategory='various')
		{
			if (!isset($this->arrModel[$intModelId])) {
				return $this->addError('(addFilePackage) Data model with Id '.$intModelId.' not exists');
			}
			if (!is_array($strPasswords)) {
				$strPasswords = trim($strPasswords);
				if (strpos($strPasswords,',')) {
					$strPasswords = explode(',',$strPasswords);
				} elseif (strpos($strPasswords,';')) {
					$strPasswords = explode(';',$strPasswords);
				}
			}
			if ($strPasswords) {
    			for ($a=0; $a<count($strPasswords); $a++) {
                    $strPasswords[$a] = trim($strPasswords[$a]);
                }
			}
			$intNewPackageId = count($this->arrModel[$intModelId]['packages']);
			$this->arrModel[$intModelId]['packages'][$intNewPackageId]['name'] = trim($strName);
			$this->arrModel[$intModelId]['packages'][$intNewPackageId]['passwords'] = $strPasswords;
			$this->arrModel[$intModelId]['packages'][$intNewPackageId]['comment'] = trim($strComment);
			$this->arrModel[$intModelId]['packages'][$intNewPackageId]['category'] = trim($strCategory);
			$this->arrModel[$intModelId]['packages'][$intNewPackageId]['links'] = array();
			return $intNewPackageId;
		}

		// Add a link to a file package
		public function addLink($intModelId,$intPackageId,$strUrl,$strFilename='',$intFilesize=0) {
			if (!isset($this->arrModel[$intModelId]['packages'][$intPackageId]['links'])) {
				return $this->addError('(addLink) Package with Id '.$intPackageId.' not exists');
			}
			$arrLink = array('url' => trim($strUrl),
							 'filename' => trim($strFilename),
							 'size' => trim($intFilesize));
			array_push($this->arrModel[$intModelId]['packages'][$intPackageId]['links'],$arrLink);
			return true;
		}

        /**
         * DLC
         */

		// Create a DLC stream
		public function createDLC($intModelId,$strApplication=NULL,$strUrl=NULL,$strVersion=NULL) {
			if (!isset($this->arrModel[$intModelId])) {
				return $this->addError('(createDLC) Data model with Id '.$intModelId.' not exists');
			}
			if (!trim($strApplication)) $strApplication = self::dlc_content_generator_name;
			if (!trim($strUrl)) $strUrl = self::dlc_content_generator_url;
			if (!trim($strVersion)) $strVersion = self::dlc_api_version;
            # Create XML
			$strXML = '<dlc>';
			$strXML.= '<header>';
			$strXML.= '<generator>';
			$strXML.= '<app>'.$this->dlcDataEncode($strApplication).'</app>';
			$strXML.= '<version>'.$this->dlcDataEncode($strApplication).'</version>';
			$strXML.= '<url>'.$this->dlcDataEncode($strApplication).'</url>';
			$strXML.= '</generator>';
			$strXML.= '<tribute>';
			$strXML.= '<name>'.$this->dlcDataEncode($this->arrModel[$intModelId]['uploader']).'</name>';
			$strXML.= '</tribute>';
			$strXML.= '<dlcxmlversion>'.$this->dlcDataEncode('20_02_2008').'</dlcxmlversion>';
			$strXML.= '</header>';
			$strXML.= '<content>';
			for ($a=0; $a<count($this->arrModel[$intModelId]['packages']); $a++) {
				$strXML.= '<package name="'.$this->dlcDataEncode($this->arrModel[$intModelId]['packages'][$a]['name']).'"';
					$strTmp = $this->arrModel[$intModelId]['packages'][$a]['passwords'];
					if (is_array($strTmp)) {
            			for ($b=0; $b<count($strTmp); $b++) {
                            $strTmp[$b] = '"'.$strTmp[$b].'"';
                        }
						$strTmp = '{'.implode(', ',$strTmp).'}';
					}
					if ($strTmp) $strXML.= ' passwords="'.$this->dlcDataEncode($strTmp).'"';
					$strTmp = $this->arrModel[$intModelId]['packages'][$a]['comment'];
					if ($strTmp) $strXML.= ' comment="'.$this->dlcDataEncode($strTmp).'"';
					$strTmp = $this->arrModel[$intModelId]['packages'][$a]['category'];
					if ($strTmp) $strXML.= ' category="'.$this->dlcDataEncode($strTmp).'"';
				$strXML.= '>';
				for ($b=0; $b<count($this->arrModel[$intModelId]['packages'][$a]['links']); $b++) {
    				$strXML.= '<file>';
    				$strXML.= '<url>'.$this->dlcDataEncode($this->arrModel[$intModelId]['packages'][$a]['links'][$b]['url']).'</url>';
    				$strXML.= '<filename>'.$this->dlcDataEncode($this->arrModel[$intModelId]['packages'][$a]['links'][$b]['filename']).'</filename>';
    				$strXML.= '<size>'.$this->dlcDataEncode($this->arrModel[$intModelId]['packages'][$a]['links'][$b]['size']).'</size>';
    				$strXML.= '</file>';
    			}
				$strXML.= '</package>';
			}
			$strXML.= '</content>';
			$strXML.= '</dlc>';
			# Encoding XML
            $strXML = base64_encode($strXML);
			# Building keys
			$boolResult = $this->getDLCCacheKeys($strKey,$strEncryptKey,$intUpdateTime);
			if (($boolResult == false) || ($intUpdateTime < (time()-self::dlc_key_pair_expires_after))) {
				$strKey = substr(md5(md5(time().rand(0,10000)).rand(0,10000)),0,16);
				$strEncryptKey = NULL;
	            $arrServices = array();
	        	if (self::dlccrypt_services_mirror_1) array_push($arrServices, self::dlccrypt_services_mirror_1);
	        	if (self::dlccrypt_services_mirror_2) array_push($arrServices, self::dlccrypt_services_mirror_2);
	        	if (self::dlccrypt_services_mirror_3) array_push($arrServices, self::dlccrypt_services_mirror_3);
	        	if (self::dlccrypt_services_mirror_4) array_push($arrServices, self::dlccrypt_services_mirror_4);
	            shuffle($arrServices);
	            if (strlen(self::dlccrypt_mainservices) > 10) {
	                $strEncryptKey = $this->callDLCService(self::dlccrypt_mainservices,$strKey);
	            }
	            if (!$strEncryptKey && $arrServices) {
	            	foreach ($arrServices as $strService) {
	                    $strEncryptKey = $this->callDLCService($strService,$strKey);
	                }
	            }
	            if (!$strEncryptKey) {
	                return $this->addError('(createDLC) Could not encrypt key');
	            }
	            if (!$this->setDLCCacheKeys($strKey,$strEncryptKey)) {
	                return $this->addError('(createDLC) Could not save cache file for keys');
	            }
			}
			if (!$strKey || !$strEncryptKey) {
	            return $this->addError('(createDLC) Keys are empty');
			}

            # Build DLC Stream
            $hdlDLCCrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
            @mcrypt_generic_init($hdlDLCCrypt,$strKey,$strKey);
            $strStream = mcrypt_generic($hdlDLCCrypt,$strXML);
            mcrypt_generic_deinit($hdlDLCCrypt);
            mcrypt_module_close($hdlDLCCrypt);
            $strStream = base64_encode($strStream);
            unset($hdlDLCCrypt);
            /*
            // Decrypt
            $hdlDLCCrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
            @mcrypt_generic_init($hdlDLCCrypt,$strKey,$strKey);
            $strOrgStream = mdecrypt_generic($hdlDLCCrypt, base64_decode($strStream));
            mcrypt_generic_deinit($hdlDLCCrypt);
            mcrypt_module_close($hdlDLCCrypt);
            echo '<hr>'.nl2br(htmlentities(base64_decode($strOrgStream))).'</hr>';
            */
            return $strStream.$strEncryptKey;
		}

		protected function setDLCCacheKeys($strPlainKey,$strEncryptKey) {
			if (!file_exists(self::dlc_cache_keys_filename)||is_writable(self::dlc_cache_keys_filename)) {
			    if (!$hdlFile = fopen(self::dlc_cache_keys_filename,"w+")) {
					return $this->addError('(setDLCKey2Cache) Can not open file '.self::dlc_cache_keys_filename);
			    }
			    $strCacheContent = $strPlainKey.chr(13).$strEncryptKey.chr(13).time();
			    if (!fwrite($hdlFile, $strCacheContent)) {
					return $this->addError('(setDLCKey2Cache) Can not write in file '.self::dlc_cache_keys_filename);
			    }
			    fclose($hdlFile);
			} else {
				return $this->addError('(setDLCKey2Cache) file '.self::dlc_cache_keys_filename.' not writeable');
			}
			return true;
		}

		protected function getDLCCacheKeys(&$strPlainKey=null,&$strEncryptKey=null,&$intUpdateTime=0) {
			if (is_readable(self::dlc_cache_keys_filename)) {
				$strCacheContent = trim(file_get_contents(self::dlc_cache_keys_filename));
				if (!$strCacheContent) {
					return false;
				}
				$arrCacheContent = explode(chr(13),$strCacheContent);
				if ((!$arrCacheContent) || (count($arrCacheContent) <> 3)) {
					return false;
				}
				$strPlainKey 	= $arrCacheContent[0];
				$strEncryptKey 	= $arrCacheContent[1];
				$intUpdateTime 	= $arrCacheContent[2];
			} else {
				return $this->addError('(getDLCKey2Cache) Can not read file '.self::dlc_cache_keys_filename);
			}
			return true;
		}

		protected function dlcDataEncode($strValue) {
			if ($strValue == NULL) $strValue = 'n.A.';
			return base64_encode(trim($strValue));
		}

        protected function callDLCService($strService,$strKey) {
    		$arrUrl = parse_url($strService);
    		$hdlSock = @fsockopen($arrUrl["host"], 80);
    		if (!$hdlSock) return $strService;
    		fputs($hdlSock, "GET ".$arrUrl["path"]." HTTP/1.1\r\n");
    		fputs($hdlSock, "Host: ".$arrUrl["host"]."\r\n");
    		fputs($hdlSock, "Content-type: application/x-www-form-urlencoded\r\n");
    		fputs($hdlSock, "Connection: close\r\n\r\n");
    		$strResult = '';
    		while(!feof($hdlSock)) {
    			$strResult .= fgets($hdlSock, 1024);
    			if (strpos($strResult,"Location:") > 0) {
        			$arrTmp = explode("Location:",$strResult);
    			    $strService = trim($arrTmp[1]);
                    break;
    			}
    		}
    		fclose($hdlSock);
        	if (!$strService) {
        		return $this->addError('(callDLCService) Could not resolve '.$strService);
        	}
        	$strResult = $this->postRequest($strService,"&data=".$strKey."&lid=".base64_encode(self::dlc_content_generator_id."_".self::dlccrypt_mainservices."_".self::dlc_key_pair_expires_after));
        	if (empty($strResult)){
                return $this->addError('(callDLCService) Could not get Key from '.$strService);
         	}
        	if (!strpos($strResult,'</rc>')) {
        		return $this->addError('(callDLCService) Service not available '.$strService);
        	}
            $strKey = explode('<rc>', $strResult, 2);
            $strKey = @substr($strKey[1],0,@strpos($strKey[1],'</rc>'));
        	if (empty($strKey) || strlen($strKey)!=88) {
        		return $this->addError('(callDLCService) OLD CLIENT OR SERVER VERSION');
        	}
        	return $strKey;
        }

        /**
         * CCF
         */

        // Create a CCF stream
        public function createCCF($intModelId) {
            if (!self::ccf_key_10) {
                return $this->addError("(createCCF) CCF Keyfile is not defined");
            }
			if (!isset($this->arrModel[$intModelId])) {
				return $this->addError('(createDLC) Data model with Id '.$intModelId.' not exists');
			}
            # Create XML
			$strXML = '<?xml version="1.0" encoding="utf-8"?>';
			$strXML.= '<CryptLoad>';
			for ($a=0; $a<count($this->arrModel[$intModelId]['packages']); $a++) {
                $strXML.= '<Package service="" name="'.$this->ccfDataEncode($this->arrModel[$intModelId]['packages'][$a]['name']).'" url="Directlinks">';
				$strXML.= '<Options>';
					$strTmp = $this->arrModel[$intModelId]['packages'][$a]['comment'];
					if (!trim($strTmp)) $strTmp = 'create by DLCAPI';
					$strXML.= ($strTmp) ? '<Kommentar>'.$this->ccfDataEncode($strTmp).'</Kommentar>' : '<Kommentar />';
					$strTmp = $this->arrModel[$intModelId]['packages'][$a]['passwords'];
					if (is_array($strTmp)) {
						$strTmp = implode(',',$strTmp);
					}
					$strXML.= ($strTmp) ? '<Passwort>'.$this->ccfDataEncode($strTmp).'</Passwort>' : '<Passwort />';
				$strXML.= '</Options>';
				for ($b=0; $b<count($this->arrModel[$intModelId]['packages'][$a]['links']); $b++) {
    				$strXML.= '<Download Url="'.$this->ccfDataEncode($this->arrModel[$intModelId]['packages'][$a]['links'][$b]['url']).'">';
                    $strTmp = $this->arrModel[$intModelId]['packages'][$a]['links'][$b]['size'];
    				$strXML.= '<FileSize>'.$this->ccfDataEncode(($strTmp ? $strTmp : 0)).'</FileSize>';
    				$strXML.= '<Url>'.$this->ccfDataEncode($this->arrModel[$intModelId]['packages'][$a]['links'][$b]['url']).'</Url>';
    				$strTmp = $this->arrModel[$intModelId]['packages'][$a]['links'][$b]['filename'];
                    $strXML.= ($strTmp) ? '<FileName>'.$this->ccfDataEncode($strTmp).'</FileName>' : '<FileName/>';
    				$strXML.= '</Download>';
    			}
				$strXML.= '</Package>';
			}
			$strXML.= '</CryptLoad>';
               
			# Build CCF stream
    		$strXML = utf8_encode($strXML);
    		$arrKeyList = array(self::ccf_key_10,self::ccf_key_08,self::ccf_key_07);
    		$arrIVList = array(self::ccf_iv_10,self::ccf_iv_08,self::ccf_iv_07);
    		$hdlCCFCrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
    		mcrypt_generic_init($hdlCCFCrypt,$this->base16Decode($arrKeyList[0]),$this->base16Decode($arrIVList[0]));
    		$strStream = mcrypt_generic($hdlCCFCrypt,$strXML);
    		mcrypt_generic_deinit($hdlCCFCrypt);
    		mcrypt_module_close($hdlCCFCrypt);
    		unset($hdlCCFCrypt);
    		return $strStream;
        }

        public function ccfDataEncode($strValue) {
			return utf8_encode($strValue);
		}

        public function decryptCCF($strStream) {
    		$arrKeyList = array(self::ccf_key_10,self::ccf_key_08,self::ccf_key_07);
    		$arrIVList = array(self::ccf_iv_10,self::ccf_iv_08,self::ccf_iv_07);
    		$arrVList = array(self::ccf_id_10,self::ccf_id_08,self::ccf_id_07);
        	$a = 0;
        	$strXML = '';
        	while ($a < count($arrKeyList)){
        		$hdlCCFDecrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
        		@mcrypt_generic_init($hdlCCFDecrypt, $this->base16Decode($arrKeyList[$a]),$this->base16Decode($arrIVList[$a]));
        		$strOrgStream = $this->filterString(mdecrypt_generic($hdlCCFDecrypt, $strStream));
        		mcrypt_generic_deinit($hdlCCFDecrypt);
        		mcrypt_module_close($hdlCCFDecrypt);
        		if (strpos(strtolower($strOrgStream),"cryptload")>0) {
        			$strXML = trim($strOrgStream);
        			break;
        		}
        		$a++;
        	}
        	unset($hdlCCFDecrypt);
        	return $strXML;
        }

        /**
         * RSDF
         */

		// Create a RSDF stream
        function createRSDF($intModelId){
            if (!self::rsdf_key || !self::rsdf_iv) {
                return $this->addError('(createRSDF) RSDF Keyfile is not defined');
            }
            if (!isset($this->arrModel[$intModelId])) {
				return $this->addError('(createRSDF) Data model with Id '.$intModelId.' not exists');
			}
            $strReturn = '';
            $strKey = $this->base16Decode(self::rsdf_key);
            $strIv  = $this->base16Decode(self::rsdf_iv);
            # Build RSDF stream
            $hdlRSDFCrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CFB,'');
            mcrypt_generic_init($hdlRSDFCrypt,$strKey,$strIv);
            for ($a=0; $a<count($this->arrModel[$intModelId]['packages']); $a++) {
                for ($b=0; $b<count($this->arrModel[$intModelId]['packages'][$a]['links']); $b++) {
                    $strReturn.= base64_encode(mcrypt_generic($hdlRSDFCrypt,$this->arrModel[$intModelId]['packages'][$a]['links'][$b]['url']));
                    $strReturn.= "\r\n";
                }
            }
            mcrypt_generic_deinit($hdlRSDFCrypt);
            mcrypt_module_close($hdlRSDFCrypt);
            unset($hdlRSDFCrypt);
            return $this->base16Encode($strReturn);
        }

        // Send a post request
        protected function postRequest($strUrl,$strData,$arrHeader=array()) {
        	$arrUrl = parse_url($strUrl);
        	$hdlSock = @fsockopen($arrUrl["host"], 80);
        	if (!$hdlSock)return NULL;
        	fputs($hdlSock,"POST ".$arrUrl["path"].(isset($arrUrl["query"])?"?".$arrUrl["query"]:"")." HTTP/1.1");
        	fputs($hdlSock,"\r\n");
        	fputs($hdlSock, "Host: ".$arrUrl["host"]);
        	fputs($hdlSock,"\r\n");
        	if ($arrHeader){
                foreach ($arrHeader as $strKey => $strValue) {
        			 fputs($hdlSock, $strKey.": ".$strValue);
        			 fputs($hdlSock,"\r\n");
           	 	}
        	}
        	fputs($hdlSock,"Content-type: application/x-www-form-urlencoded");
        	fputs($hdlSock,"\r\n");
        	fputs($hdlSock, "Content-length: ". strlen($strData));
        	fputs($hdlSock,"\r\n");
        	if ($arrHeader && isset($arrHeader["Keep-Alive"])) {
        		fputs($hdlSock,"Connection: keep-alive");
        		fputs($hdlSock,"\r\n");
        	} else {
        		fputs($hdlSock,"Connection: close");
        		fputs($hdlSock,"\r\n");
        	}
        	fputs($hdlSock,"\r\n");
        	fputs($hdlSock, $strData);
            $strResult = '';
        	while(!feof($hdlSock)) {
        		$strResult.= fgets($hdlSock, 128);
        	}
        	fclose($hdlSock);
        	return $strResult;
        }

        // Decoding string as Base 16
        protected function base16Decode($strValue) {
        	$strReturn = '';
        	for($a=0; $a<strlen($strValue); $a+=2){
        		$strTmp = substr($strValue,$a,2);
        		$int = hexdec($strTmp);
        		$strReturn.= chr($int);
        	}
        	return $strReturn;
        }

        // Encoding string as Base 16
        protected function base16Encode($strValue) {
        	$strReturn = '';
        	for($a=0; $a<strlen($strValue); $a++){
        		$strTmp = ord($strValue[$a]);
        		$strHex = dechex($strTmp);
        		while (strlen($strHex) < 2 ) {
                    $strHex = "0".$strHex;
                }
        		$strReturn.=$strHex;
        	}
        	return $strReturn;
        }

        // Filter a string
        protected function filterString($strValue) {
        	$strResult = '';
        	$strAllowed = 'QWERTZUIOPÜASDFGHJKLÖÄYXCVBNMqwertzuiopasdfghjklyxcvbnm;:,._-&%(){}#~+ 1234567890<>=\'"/';
            $chrChar = '';
        	for ($a=0; $a<strlen($strValue); $a++){
        		if (!(strpos($strAllowed,($chrChar=substr($strValue,$a,1))) === false)) {
        			$strResult.= $chrChar;
        		}
            }
            return $strResult;
    	}
	}
?>