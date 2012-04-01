<?php
namespace CLI\Threading;

class CommunicationSet {
	private $parentSocket;
	private $childSocket;

	function __construct(){
		$sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

		list($this->parentSocket,$this->childSocket) = $sockets;
	}

	function getChildCommunicator(){
		return new Communication($this->childSocket);
	}
	function getParentCommunicator(){
		return new Communication($this->parentSocket);
	}
}