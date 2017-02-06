<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class FileResponse extends RoutemasterResponse
{
	protected $file;

	public function __construct($filePath, $fileName, $disableCache = false)
	{
		parent::__construct([]);

		if (substr($filePath, -4) == '.pdf') {
			$this->headers[] = 'Content-Type: application/pdf';
		}
		if ($disableCache) {
			$this->headers[] = "Cache-Control: no-cache, no-store, must-revalidate"; // HTTP 1.1
			$this->headers[] = "Pragma: no-cache"; // HTTP 1.0
			$this->headers[] = "Expires: 0"; // Proxies
		}
		$this->headers[] = 'Content-Disposition: inline; filename=' . $fileName;
		$this->file = $filePath;
	}

	protected function render()
	{
		readfile($this->file);
	}


}