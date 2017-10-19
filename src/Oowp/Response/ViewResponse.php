<?php

namespace Outlandish\Wordpress\Routemaster\Oowp\Response;

use Outlandish\Wordpress\Routemaster\Oowp\View\RoutemasterOowpView;
use Outlandish\Wordpress\Routemaster\Response\RoutemasterResponse;

class ViewResponse extends RoutemasterResponse
{
	/** @var RoutemasterOowpView */
	public $view;

	protected function render()
	{
		if ($this->view) {
			foreach ($this->outputArgs as $key=>$value) {
				$this->view->$key = $value;
			}
			$this->view->render();
		}
	}
}