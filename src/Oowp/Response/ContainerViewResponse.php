<?php

namespace Outlandish\Wordpress\Routemaster\Oowp\Response;

use Outlandish\Wordpress\Oowp\Views\OowpView;
use Outlandish\Wordpress\Routemaster\Oowp\View\ContainerView;
use Outlandish\Wordpress\Routemaster\Response\RoutemasterResponse;

class ContainerViewResponse extends RoutemasterResponse
{
	/** @var OowpView */
	public $view;

	/**
	 * @param OowpView $view
	 */
	public function __construct($view)
	{
		parent::__construct([]);
		$this->view = $this->createContainerView($view);
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