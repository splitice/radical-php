<?php
namespace Utility\Net\HTTP\Curl;
/**
 * IOStream class to manage curl input/output
 *
 * @author JokerHacker
 */

class IOStream {
        private $transferoutput = null;
        private $transferinput = null;
        private $erroroutput = null;
        private $headeroutput = null;

        function __construct(){
           $this->setTransOutput(STDOUT);
           $this->setTransInput(null);
           $this->setErrorOutput(STDERR);
           $this->setHeaderOutput(null);
        }
        /**
         * File to send curl output to
         * 
         * @param string $file
         */
        function setTransOutput($file){
            if($file)
                $this->transferoutput = $file;
        }

        /**
         * File to read input from
         * 
         * @param string $file
         */
        function setTransInput($file){
            $this->transferinput = $file;
        }

        /**
         * File to write output to
         * 
         * @param string $file
         */
        function setErrorOutput($file){
            $this->erroroutput = $file;
        }

        /**
         * File to write headers to
         * 
         * @param string $file
         */
        function setHeaderOutput($file){
            $this->headeroutput = $file;
        }

        /**
         * Apply to a curl object
         * 
         * @param Curl $curl
         */
        function apply(Curl $curl){
            $curl->curl[CURLOPT_FILE] = fopen($this->transferoutput, 'w+');
            $curl->curl[CURLOPT_INFILE] = fopen($this->transferinput, 'r');
            $curl->curl[CURLOPT_STDERR] = fopen($this->erroroutput, 'w+');
            $curl->curl[CURLOPT_WRITEHEADER] = fopen($this->headeroutput, 'w+');
        }
        
        function toArray(){
        	return array(
        			'transfer_output'=>$this->transferoutput,
        			'transfer_input'=>$this->transferinput,
        			'error_output'=>$this->erroroutput,
        			'header_output'=>$this->headeroutput
        			);
        }

        function __toString(){
            return $this->transferoutput . PHP_EOL .
            $this->transferinput . PHP_EOL .
            $this->erroroutput . PHP_EOL .
            $this->headeroutput;
        }
}
?>
