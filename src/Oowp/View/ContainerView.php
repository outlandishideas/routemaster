<?php

namespace Outlandish\Wordpress\Routemaster\Oowp\View;

use Outlandish\Wordpress\Oowp\Views\PostView;

class ContainerView extends PostView
{
	/** @var RoutemasterOowpView */
	public $content;

	/**
	 * @param RoutemasterOowpView $content
	 */
	public function __construct($content)
	{
		parent::__construct();
		$this->content = $content;
	}


	public function render($args = [])
	{
		?>
		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head></head>
		<body>
		<?php
		if ($this->content) {
			$this->content->render($args);
		}
		?>
		</body>
		</html>
		<?php
	}
}