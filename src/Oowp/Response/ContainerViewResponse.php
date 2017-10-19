<?php

namespace Outlandish\Wordpress\Routemaster\Oowp\Response;

use Outlandish\Wordpress\Routemaster\Oowp\View\ContainerView;
use Outlandish\Wordpress\Routemaster\Oowp\View\RoutemasterOowpView;
use Outlandish\Wordpress\Routemaster\Response\RoutemasterResponse;

class ContainerViewResponse extends RoutemasterResponse
{
	/** @var RoutemasterOowpView */
	public $view;

	/**
	 * @param RoutemasterOowpView $renderable
	 */
	public function __construct($renderable)
	{
		parent::__construct([]);
		$this->view = $this->createContainerView($renderable);
	}

	protected function createContainerView($view)
	{
		return new ContainerView($view);
	}

	protected function render()
	{
		if ($this->view) {
			foreach ($this->outputArgs as $name=>$value) {
				$this->view->$name = $value;
			}
			$this->view->render();
		}
	}
}