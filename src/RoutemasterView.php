<?php

namespace Outlandish\Wordpress\Routemaster;

class RoutemasterView implements RoutemasterViewInterface {
	/** @var $content string The content to be displayed in the layout */
	public $content;
	public $logDebug = true;

	public function render($viewFile, $layoutFile, $variables = array()) {
		global $post;
		$this->post = $post;

		ob_start();
		if (defined('WP_DEBUG') && WP_DEBUG && $this->logDebug) {
		    echo "\n\n<!-- start $viewFile -->\n\n";
        }
		include $viewFile;
		if (defined('WP_DEBUG') && WP_DEBUG && $this->logDebug) {
		    echo "\n\n<!-- end $viewFile -->\n\n";
        }
		$this->content = ob_get_clean();

		if ($layoutFile) {
			include($layoutFile);
		} else {
			echo $this->content;
		}
	}
}