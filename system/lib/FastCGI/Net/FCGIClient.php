<?php
namespace Net;

class FCGIClient {
	const VERSION_1 = 1;
	const BEGIN_REQUEST = 1;
	const ABORT_REQUEST = 2;
	const END_REQUEST = 3;
	const PARAMS = 4;
	const STDIN = 5;
	const STDOUT = 6;
	const STDERR = 7;
	const DATA = 8;
	const GET_VALUES = 9;
	const GET_VALUES_RESULT = 10;
	const UNKNOWN_TYPE = 11;
	const MAXTYPE = self::UNKNOWN_TYPE;
	const RESPONDER = 1;
	const AUTHORIZER = 2;
	const FILTER = 3;
	const REQUEST_COMPLETE = 0;
	const CANT_MPX_CONN = 1;
	const OVERLOADED = 2;
	const UNKNOWN_ROLE = 3;
	const MAX_CONNS = 'MAX_CONNS';
	const MAX_REQS = 'MAX_REQS';
	const MPXS_CONNS = 'MPXS_CONNS';
	const HEADER_LEN = 8;

	/**
	 * Socket
	 *
	 * @var Resource
	 */
	private $_sock = null;

	/**
	 * Host
	 *
	 * @var String
	 */
	private $_host = null;

	/**
	 * Port
	 *
	 * @var Integer
	 */
	private $_port = null;

	/**
	 * Keep Alive
	 *
	 * @var Boolean
	 */
	private $_keepAlive = false;

	/**
	 * Constructor
	 *
	 * @param String $host
	 *        	Host of the FastCGI application
	 * @param Integer $port
	 *        	Port of the FastCGI application
	 */
	public function __construct($host, $port) {
		$this->_host = $host;
		$this->_port = $port;
	}

	/**
	 * Define whether or not the FastCGI application should keep the connection
	 * alive at the end of a request
	 *
	 * @param Boolean $b
	 *        	true if the connection should stay alive, false otherwise
	 */
	public function setKeepAlive($b) {
		$this->_keepAlive = ( boolean ) $b;
		if (! $this->_keepAlive && $this->_sock) {
			$this->close ();
		}
	}

	/**
	 * Get the keep alive status
	 *
	 * @return Boolean true if the connection should stay alive, false otherwise
	 */
	public function getKeepAlive() {
		return $this->_keepAlive;
	}

	/**
	 * Create a connection to the FastCGI application
	 */
	private function connect() {
		if (! $this->_sock || feof ( $this->_sock )) {
			$this->_sock = fsockopen ( $this->_host, $this->_port, $errno, $errstr, 5 );
			if (! $this->_sock) {
				throw new \Exception ( 'Unable to connect to FastCGI application' );
			}
			stream_set_timeout ( $this->_sock, 300 );
		}
	}
	function close() {
		if ($this->_sock) {
			// fclose($this->_sock);
			// $this->_sock = null;
		}
	}
	function __destruct() {
		// $this->close();
	}

	/**
	 * Build a FastCGI packet
	 *
	 * @param Integer $type
	 *        	Type of the packet
	 * @param String $content
	 *        	Content of the packet
	 * @param Integer $requestId
	 *        	RequestId
	 */
	private function buildPacket($type, $content, $requestId = 1) {
		$clen = strlen ( $content );
		return chr ( self::VERSION_1 )         /* version */
		. chr ( $type )                    /* type */
		. chr ( ($requestId >> 8) & 0xFF ) /* requestIdB1 */
		. chr ( $requestId & 0xFF )        /* requestIdB0 */
		. chr ( ($clen >> 8) & 0xFF )     /* contentLengthB1 */
		. chr ( $clen & 0xFF )             /* contentLengthB0 */
		. chr ( 0 )                        /* paddingLength */
		. chr ( 0 )                        /* reserved */
		. $content; /* content */
	}

	/**
	 * Build an FastCGI Name value pair
	 *
	 * @param String $name
	 *        	Name
	 * @param String $value
	 *        	Value
	 * @return String FastCGI Name value pair
	 */
	private function buildNvpair($name, $value) {
		$nlen = strlen ( $name );
		$vlen = strlen ( $value );
		if ($nlen < 128) {
			/* nameLengthB0 */
			$nvpair = chr ( $nlen );
		} else {
			/* nameLengthB3 & nameLengthB2 & nameLengthB1 & nameLengthB0 */
			$nvpair = chr ( ($nlen >> 24) | 0x80 ) . chr ( ($nlen >> 16) & 0xFF ) . chr ( ($nlen >> 8) & 0xFF ) . chr ( $nlen & 0xFF );
		}
		if ($vlen < 128) {
			/* valueLengthB0 */
			$nvpair .= chr ( $vlen );
		} else {
			/* valueLengthB3 & valueLengthB2 & valueLengthB1 & valueLengthB0 */
			$nvpair .= chr ( ($vlen >> 24) | 0x80 ) . chr ( ($vlen >> 16) & 0xFF ) . chr ( ($vlen >> 8) & 0xFF ) . chr ( $vlen & 0xFF );
		}
		/* nameData & valueData */
		return $nvpair . $name . $value;
	}

	/**
	 * Read a set of FastCGI Name value pairs
	 *
	 * @param String $data
	 *        	Data containing the set of FastCGI NVPair
	 * @return array of NVPair
	 */
	private function readNvpair($data, $length = null) {
		$array = array ();

		if ($length === null) {
			$length = strlen ( $data );
		}

		$p = 0;

		while ( $p != $length ) {
				
			$nlen = ord ( $data {$p ++} );
			if ($nlen >= 128) {
				$nlen = ($nlen & 0x7F << 24);
				$nlen |= (ord ( $data {$p ++} ) << 16);
				$nlen |= (ord ( $data {$p ++} ) << 8);
				$nlen |= (ord ( $data {$p ++} ));
			}
			$vlen = ord ( $data {$p ++} );
			if ($vlen >= 128) {
				$vlen = ($nlen & 0x7F << 24);
				$vlen |= (ord ( $data {$p ++} ) << 16);
				$vlen |= (ord ( $data {$p ++} ) << 8);
				$vlen |= (ord ( $data {$p ++} ));
			}
			$array [substr ( $data, $p, $nlen )] = substr ( $data, $p + $nlen, $vlen );
			$p += ($nlen + $vlen);
		}

		return $array;
	}

	/**
	 * Decode a FastCGI Packet
	 *
	 * @param String $data
	 *        	String containing all the packet
	 * @return array
	 */
	private function decodePacketHeader($data) {
		$ret = array ();
		$ret ['version'] = ord ( $data {0} );
		$ret ['type'] = ord ( $data {1} );
		$ret ['requestId'] = (ord ( $data {2} ) << 8) + ord ( $data {3} );
		$ret ['contentLength'] = (ord ( $data {4} ) << 8) + ord ( $data {5} );
		$ret ['paddingLength'] = ord ( $data {6} );
		$ret ['reserved'] = ord ( $data {7} );
		return $ret;
	}
	private static function fread($socket, $len) {
		$buffer = '';
		$start_time = time();
		set_socket_blocking ( $socket, 1 );

		while ( strlen ( $buffer ) != $len ) {
			if (feof ( $socket ))
				return $buffer;
			
			$r = array();
			$n = array ();
			while(!count($r)){
				$r = array (
						$socket
				);
				stream_select ( $r, $n, $n, 10 );
				
				if((time() - $start_time) > 300){
					return false;
				}
			}
				
			$b = fread ( $socket, $len - strlen ( $buffer ) );
				
			if ($b === false)
				return false;
				
			$buffer .= $b;
			if (strlen ( $buffer ) != $len) {
				
			}
		}

		return $buffer;
	}

	/**
	 * Read a FastCGI Packet
	 *
	 * @return array
	 */
	private function readPacket($socket = null) {
		if ($socket === null)
			$socket = $this->_sock;

		$packet = self::fread ( $socket, self::HEADER_LEN );
		if ($packet !== false && ! empty ( $packet )) {
			if (strlen ( $packet ) != self::HEADER_LEN) {
				throw new FCGIBadRequest ();
			}
				
			$resp = $this->decodePacketHeader ( $packet );
				
			if ($len = $resp ['contentLength'] + $resp ['paddingLength']) {
				$resp ['content'] = substr ( self::fread ( $socket, $len ), 0, $resp ['contentLength'] );
			} else {
				$resp ['content'] = '';
			}
				
			return $resp;
		} else {
			return false;
		}
	}

	/**
	 * Get Informations on the FastCGI application
	 *
	 * @param array $requestedInfo
	 *        	information to retrieve
	 * @return array
	 */
	public function getValues(array $requestedInfo) {
		$this->connect ();

		$request = '';
		foreach ( $requestedInfo as $info ) {
			$request .= $this->buildNvpair ( $info, '' );
		}
		fwrite ( $this->_sock, $this->buildPacket ( self::GET_VALUES, $request, 0 ) );

		$resp = $this->readPacket ();
		if ($resp ['type'] == self::GET_VALUES_RESULT) {
			return $this->readNvpair ( $resp ['content'], $resp ['length'] );
		} else {
			throw new \Exception ( 'Unexpected response type, expecting GET_VALUES_RESULT' );
		}
	}
	function _read_packet($socket = null) {
		$response = array (
				self::STDOUT => '',
				self::STDERR => ''
		);
		$cl = false;
		do {
			$resp = $this->readPacket ( $socket );
			if (! is_array ( $resp )) {
				if ($cl)
					break;
				throw new FCGIBadRequest ();
			}
			if ($resp ['type'] == self::STDOUT || $resp ['type'] == self::STDERR) {
				$response [$resp ['type']] .= $resp ['content'];
			}
			$cl = true;
		} while ( $resp && $resp ['type'] != self::END_REQUEST );

		switch (ord ( $resp ['content'] {4} )) {
			case self::CANT_MPX_CONN :
				throw new \Exception ( 'This app can\'t multiplex [CANT_MPX_CONN]' );
				break;
			case self::OVERLOADED :
				throw new \Exception ( 'New request rejected; too busy [OVERLOADED]' );
				break;
			case self::UNKNOWN_ROLE :
				throw new \Exception ( 'Role value not known [UNKNOWN_ROLE]' );
				break;
			case self::REQUEST_COMPLETE :
				return $response;
		}
	}

	/**
	 * Execute a request to the FastCGI application
	 *
	 * @param array $params
	 *        	Array of parameters
	 * @param String $stdin
	 *        	Content
	 * @return String
	 */
	public function request(array $params, $stdin) {
		$k = $this->_keepAlive;
		$r = $this->request_lazy ( $params, $stdin );
		$this->_keepAlive = $k;
		return $r ();
	}
	public function request_lazy(array $params, $stdin, $redo = false) {
		$response = '';
		$this->connect ();

		$this->_keepAlive = true;

		$request = $this->buildPacket ( self::BEGIN_REQUEST, chr ( 0 ) . chr ( self::RESPONDER ) . chr ( ( int ) $this->_keepAlive ) . str_repeat ( chr ( 0 ), 5 ) );

		$paramsRequest = '';
		foreach ( $params as $key => $value ) {
			$paramsRequest .= $this->buildNvpair ( $key, $value );
		}
		if ($paramsRequest) {
			$request .= $this->buildPacket ( self::PARAMS, $paramsRequest );
		}
		$request .= $this->buildPacket ( self::PARAMS, '' );

		if ($stdin) {
			$request .= $this->buildPacket ( self::STDIN, $stdin );
		}
		$request .= $this->buildPacket ( self::STDIN, '' );

		fwrite ( $this->_sock, $request );

		// What the lazy return needs
		$sock = $this->_sock;
		$t = $this;

		// Redo Process
		$redo = $redo ? function () {
		} : function () use($params, $stdin, $t) {
			return $t->request_lazy ( $params, $stdin, true );
		};

		// Lazy return processing
		return function () use($sock, $t, $redo) {
			$data = null;
			try {
				$data = $t->_read_packet ( $sock );
			} catch ( FCGIBadRequest $ex ) {
				echo $ex->getMessage (), "\r\n";
				$t->close ();
				$data = $redo ();
				if ($data)
					return $data ();
			}
			// $t->close();
				
			// TODO: restore
			$this->_keepAlive = false;
				
			return $data;
		};
	}
}