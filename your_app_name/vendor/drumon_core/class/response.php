<?php
	
	/**
	 * Class to handler HTTP responses
	 *
	 */
	class Response {
		
		/**
		 * Http status code
		 *
		 * @var string
		 */
		public $http_status_code = 200;
		
		/**
		 * Header charset for response
		 *
		 * @var string
		 */
		public $charset = 'utf-8';
		
		/**
		 * Response dody
		 *
		 * @var string
		 */
		public $body = NULL;
		
		/**
		 * List of headers
		 *
		 * @var array
		 */
		public $headers = array();
		
		/**
		 * List of all available Http status codes
		 *
		 * @var array
		 */
		private $http_status_codes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
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
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			507 => 'Insufficient Storage',
			509 => 'Bandwidth Limit Exceeded'
		);
		
		/**
		 * Set headers and return body content
		 *
		 * @return string
		 */
		public function __toString() {
			
			// Set default Content-Type if not exist
			if (!isset($this->headers['Content-Type'])) {
				$this->headers['Content-Type'] = 'text/html; charset='.$this->charset;
			}
			
			// Set status header. ex "Status: 200 Ok"
			$this->headers['Status'] = $this->http_status_code.' '.$this->http_status_codes[$this->http_status_code];

			if (!headers_sent()) {
				// Example: "HTTP/1.0 200 ok"
				header($_SERVER["SERVER_PROTOCOL"].' '.$this->http_status_code.' '.$this->http_status_codes[$this->http_status_code]);
				
				// Set headers
				foreach ($this->headers as $key => $value) {
					header($key.': '.$value);
				}
			}
			
			return (string) $this->body;
		}
		
	}
?>