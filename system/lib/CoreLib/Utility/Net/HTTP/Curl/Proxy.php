<?php
namespace Utility\Net\HTTP\Curl;
use Basic\Structs\UserPass;
/**
 * Proxy class used to define proxy for curl connections
 *
 * @author JokerHacker
 */

class Proxy {
        private $address = null;
        private $port = null;
        private $userpass = null;
        private $httptunnel = null;
        private $type = null;

        function __construct($address=null){
            if($address)
                $this->address = $adress;
            $this->port = 80;
            $this->type = CURLPROXY_HTTP;
            $this->userpass = new UserPass('','');
            $this->httptunnel = false;
        }
        function setAddress($address=null){
            /* TODO: use URL Class */
            if($address)
                $this->address = $adress;
        }

        function setPort($port){
            if(is_int($port))
                $this->port = $port;
        }

        function setAuth(UserPass $userpass){
            $this->userpass = $userpass;
        }

        function setHttpTunnel($is_tunnel){
            $this->httpTunnel = (bool)$is_tunnel;
        }

        function setType($type){
            switch ($type) {
                case 'http':
                    $this->type = CURLPROXY_HTTP;
                    break;
                case 'socks4':
                    $this->type = CURLPROXY_SOCKS4;
                    break;
                case 'socks5':
                    $this->type = CURLPROXY_SOCKS5;
                    break;
                default:
                    throw new \Exception('Invalid Http Tunnel');
            }
        }

        function apply(Curl &$curl){
            $curl->curl[CURLOPT_PROXY] = $this->address;
            $curl->curl[CURLOPT_PROXYPORT] = $this->port;
            $curl->curl[CURLOPT_PROXYUSERPWD] = (string)$this->userpass;
            $curl->curl[CURLOPT_HTTPPROXYTUNNEL] = $this->httptunnel;
            $curl->curl[CURLOPT_PROXYTYPE] = $this->type;
        }

        function __toString(){
            $string = '';
            switch ($this->type) {
                case 0 :
                    $string .= 'http';
                    break;
                case 4 :
                    $string .= 'socks4';
                    break;
                case 5 :
                    $string .= 'socks5';
                    break;
            }
            $string .= '://';
            if($this->userpass->hasDetails())
                $string .= (string)$this->userpass . '@';
            $string .= $this->address . ':' . $this->port . '/';
            return $string;
        }
}
?>
