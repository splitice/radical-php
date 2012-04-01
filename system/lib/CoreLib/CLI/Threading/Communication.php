<?php
namespace CLI\Threading;

class Communication {
	const CHUNK_SIZE = 4096;

	private $endChar;
	public $socket;
	private $queue = array();

	function __construct($socket){
		$this->socket = $socket;
		socket_set_blocking($socket,false);
		$this->endChar = chr(6);
	}
	
	function isConnected(){
		return !feof($this->socket);
	}

	function Send($message){
		if(!$this->isConnected()){
			return false;
		}
		
		$message = serialize($message);
		$message.= $this->endChar;
		
		
		do{
			socket_set_blocking($this->socket,true);
			while(($bytes = @fwrite($this->socket, $message)) === false){
				echo (string)$socket,"\r\n";
				usleep(10000);
			}
			socket_set_blocking($this->socket,false);
			$len = strlen($message);
			if($bytes<$len){
				if($bytes){
					$message = substr($message,$bytes);
				}
				usleep(2000);
			}
		}while($bytes<$len);
	}

	function Receive(){
		if(!$this->isConnected()){
			return array();
		}
		
		$data = '';
		do{
			$temp = fread($this->socket,self::CHUNK_SIZE);
			if($temp === false || !strlen($temp)) break;
			$data .= $temp;
		}while(true);
		
		if(!$data){
			return array();
		}

		if(substr($data,-1) != $this->endChar){
			die(var_dump($data));
			throw new \Exception('Invalid IPC message');
		}

		$data = substr($data,0,-1);

		$data = explode($this->endChar, $data);

		foreach($data as $k=>$message){
			$data[$k] = unserialize($message);
		}
		
		if($this->queue){
			$data = array_merge($this->queue,$data);
			$this->queue = array();
		}

		return $data;
	}
	
	function Handle($messages){
		$ret = array();
		foreach($messages as $m){
			if($m instanceof Messages\IExecuteMessage){
				$m->Execute();
			}else{
				$ret[] = $m;
			}
		}
		return $ret;
	}
	
	function HandleReceive(){
		$messages = $this->ReceiveAndHandle();
		
		//Requeue
		$this->queue = $messages;
	}
	
	function ReceiveAndHandle(){
		$messages = $this->Receive();
		$messages = $this->Handle($messages);
		return $messages;
	}
}