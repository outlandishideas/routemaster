<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class JsonResponse extends RoutemasterResponse {
	public $status;

	public function __construct($outputArgs = [], $status = 200)
	{
		if (is_string($outputArgs)) {
			$outputArgs = ['message' => $outputArgs];
		}
		parent::__construct($outputArgs);
		$this->headers[] = "access-control-allow-origin: *";
		$this->headers[] = 'Content-type: application/json';
		$this->status = $status;
	}

	protected function render()
	{
		http_response_code($this->status);

		// zip response if accepted
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
			ob_start('ob_gzhandler');
		}

		$rendered = json_encode($this->outputArgs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		echo $rendered;
	}
}