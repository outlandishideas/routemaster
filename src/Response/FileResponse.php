<?php

namespace Outlandish\Wordpress\Routemaster\Response;

class FileResponse extends RoutemasterResponse
{
    protected $file;
    public $deleteOnSent = false;

    public function __construct($filePath, $fileName, $contentType, $asAttachment = true, $enableBrowserCache = false)
    {
        parent::__construct([]);

        $this->headers[] = 'Content-Type: ' . $contentType;

        if (!$enableBrowserCache) {
            $this->headers[] = "Cache-Control: no-cache, no-store, must-revalidate"; // HTTP 1.1
            $this->headers[] = "Pragma: no-cache"; // HTTP 1.0
            $this->headers[] = "Expires: 0"; // Proxies
        }

        $this->headers[] = 'Content-Disposition: ' . ($asAttachment ? 'attachment' : 'inline') . '; filename=' . $fileName;

        $this->file = $filePath;
    }

    protected function render()
    {
        readfile($this->file);
    }

    protected function postRender()
    {
        parent::postRender();

        if ($this->deleteOnSent) {
            unlink($this->file);
        }
    }
}