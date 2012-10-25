<?php
class View {
	/** @var $content string The content to be displayed in the layout */
	public $content;

	public function render($viewFile, $layoutFile, $variables = array()) {
        extract($variables, EXTR_OVERWRITE);
		global $wp, $post, $wp_query;
		ob_start();
		if (WP_DEBUG) echo "\n\n<!-- start $viewFile -->\n\n";
		include $viewFile;
		if (WP_DEBUG) echo "\n\n<!-- end $viewFile -->\n\n";
		$this->content = ob_get_clean();

		if ($layoutFile) {
			include($layoutFile);
		} else {
			echo $this->content;
		}
	}
}