<?php
class RoutemasterView implements RoutemasterViewInterface {
	/** @var $content string The content to be displayed in the layout */
	public $content;
	public $logDebug = true;

	public function render($viewFile, $layoutFile, $variables = array()) {
		global $post;
		$this->post = $post;

		// todo: are these two lines necessary now?
//        extract($variables, EXTR_OVERWRITE);
//		global $wp, $wp_query;


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