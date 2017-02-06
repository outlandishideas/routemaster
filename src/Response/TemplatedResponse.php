<?php

namespace Outlandish\Wordpress\Routemaster\Response;

use Outlandish\Wordpress\Routemaster\RoutemasterView;

abstract class TemplatedResponse extends RoutemasterResponse
{
	public static $viewPath = '';

	public $layout = null;
	public $viewName = null;

	protected function render()
	{
		$viewFile = self::viewFile($this->viewName);
		if (!file_exists($viewFile)) {
			die('View file not found: ' . $this->viewName);
		}
		$view = $this->createView();
		$view->view = $view;
		foreach ($this->outputArgs as $name=>$value) {
			$view->$name = $value;
		}
		$view->render($viewFile, self::viewFile($this->layout));
	}

	/**
	 * @return RoutemasterView
	 */
	protected function createView()
	{
		return new RoutemasterView();
	}

	protected static function viewFile($name)
	{
		return $name ? self::$viewPath . $name . ".php" : null;
	}

	/**
	 * Tests if a view file exists in the view path
	 * @param $name string
	 * @return bool
	 */
	public function viewExists($name)
	{
		return file_exists(self::viewFile($name));
	}
}