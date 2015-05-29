<?php

namespace Yagrysha\MVC;

class Response
{
	const TYPE_HTML = 1;
	const TYPE_TEXT = 2;
	const TYPE_XML = 3;
	const TYPE_JSON = 4;

	private $type;
	private $ctypes = [
		self::TYPE_HTML => 'text/html',
		self::TYPE_TEXT => 'text/plain',
		self::TYPE_XML => 'text/xml',
		self::TYPE_JSON => 'application/json',
	];

	private $headers = [
		'X-Powered-By: PHP/mvc'
	];
	private $contentType = 'text/html';
	private $data;
	private $statusCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended'
	);

	public function type($type)
	{
		if (isset($this->ctypes[$type])) {
			$this->type = $type;
			$this->contentType($this->ctypes[$type]);
		}
	}

	public function setData($data)
	{
		if (is_array($data)) {
			//todo render
			if (isset($data['_type'])) {
				$this->type($data['_type']);
				unset($data['_type']);
			}
			if (isset($data['_status'])) {
				$this->status($data['_status']);
				unset($data['_status']);
			}
		}
		$this->data = $data;
	}

	public function status($statusCode)
	{
		if ($this->statusCodes[$statusCode] !== null) {
			header(
				$_SERVER['SERVER_PROTOCOL'] . ' ' . $statusCode . ' ' . $this->statusCodes[$statusCode],
				true,
				$statusCode
			);
		}
	}

	public function location($uri)
	{
		header('Location: ' . $uri);
	}

	public function contentType($type)
	{
		$this->contentType = $type;
	}

	public function sendHeaders()
	{
		header('Content-Type: ' . $this->contentType . '; charset=utf-8');
		foreach ($this->headers as $header) {
			header($header);
		}
	}

	public function render()
	{
		$this->sendHeaders();
		if (self::TYPE_JSON == $this->type) {
			echo json_encode($this->data);
		} else {
			//todo renders
			echo $this->data;
		}
	}
}