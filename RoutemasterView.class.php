<?php
class RoutemasterView {
	/** @var $content string The content to be displayed in the layout */
	public $content;
	public $logDebug = true;

	public function render($viewFile, $layoutFile, $variables = array()) {
        extract($variables, EXTR_OVERWRITE);
		global $wp, $post, $wp_query;

		$this->post = $post;

		ob_start();
		if (WP_DEBUG && $this->logDebug) echo "\n\n<!-- start $viewFile -->\n\n";
		include $viewFile;
		if (WP_DEBUG && $this->logDebug) echo "\n\n<!-- end $viewFile -->\n\n";
		$this->content = ob_get_clean();

		if ($layoutFile) {
			include($layoutFile);
		} else {
			echo $this->content;
		}
	}
}