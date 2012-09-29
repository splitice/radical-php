<?php
namespace Utility\Net\Mail;

use Web\Page\Handler\SubRequest;

use Web\Page\Handler\PageBase;

use Utility\Net\Mail\Handler;

class Message {
	/**
	 * @var Handler\IMailHandler
	 */
	private $handler;
	
	private $to;
	private $from;
	private $subject;
	private $reply_to;
	private $html = false;
	
	function __construct(Handler\IMailHandler $handler = null){
		if($handler === null){
			$handler = new Handler\Internal();
		}
		$this->handler = $handler;
	}
	
	/**
	 * @return the $reply_to
	 */
	public function getReplyTo() {
		return $this->reply_to;
	}

	/**
	 * @param field_type $reply_to
	 */
	public function setReplyTo($reply_to) {
		$this->reply_to = $reply_to;
	}

	/**
	 * @return the $to
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * @return the $from
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * @return the $subject
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param field_type $to
	 */
	public function setTo($to) {
		$this->to = $to;
	}

	/**
	 * @param field_type $from
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 * @param boolean $html
	 */
	public function setHtml($html) {
		$this->html = (bool)$html;
	}

	/**
	 * @return the $html
	 */
	public function getHtml() {
		return $this->html;
	}

	/**
	 * @param field_type $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	static function body($body){
		if($body instanceof PageBase){
			$sr = new SubRequest($body);
			$body = $sr->Execute('GET');
		}
		return $body;
	}

	function send($body){
		$body = self::body($body);
		return $this->handler->Send($this,$body);
	}
}