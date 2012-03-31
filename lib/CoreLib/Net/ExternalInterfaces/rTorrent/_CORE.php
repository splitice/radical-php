<?php
namespace Net\ExternalInterfaces\rTorrent;
class _CORE {
	function Post($command,$post) {
		$post_data = xmlrpc_encode_request($command,$post);
		
		$req = curl_init ( $this->host );
		
		// Using the cURL extension to send it off,  first creating a custom header block
		$headers = array ();
		array_push ( $headers, "Content-Type: text/xml" );
		array_push ( $headers, "Content-Length: " . strlen ( $post_data ) );
		array_push ( $headers, "\r\n" );
		
		//Setting options for a secure SSL based xmlrpc server
		curl_setopt ( $req, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $req, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt ( $req, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt ( $req, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $req, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $req, CURLOPT_POSTFIELDS, $post_data );
		
		//Finally run
		$response = curl_exec ( $req );
		
		//Close the cURL connection
		curl_close ( $req );
		
		return xmlrpc_decode($response);
	}
}
?>