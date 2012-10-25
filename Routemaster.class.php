<?php

require_once('RoutemasterView.class.php');

/**
 * Base Routing/Controller/View class. Extend this in your theme.
 */
abstract class Routemaster {
	private static $instance;
	/** @var RoutemasterView View */
	protected $view;
	protected $queryArgs, $layout, $viewPath, $viewName, $requestUri;
	protected $_debug;

	protected function __construct(){
		$this->initView();
	}

	protected function initView() {
		$this->view = new RoutemasterView();
	}

	/**
	 * @static
	 * @return Routemaster|ooRoutemaster Singleton instance
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	protected abstract function routes();

	/**
	 * Main workhorse method.
	 * Attempts to match URI against routes and dispatches routing methods.
	 * Falls back to 404 if none match.
	 */
	public function route() {
		global $wp_query;

		//strip base dir and query string from request URI
		$base = dirname($_SERVER['SCRIPT_NAME']);
		$this->requestUri = preg_replace("|^$base/?|", '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$this->_debug['routes'] = $this->routes();
		$this->_debug['requestUri'] = $this->requestUri;
		//find matching route
		foreach ($this->routes() as $pattern => $action) {
			if (preg_match($pattern, $this->requestUri, $matches)) {
				$this->_debug['matched_route'] = $pattern;
				$this->_debug['matched_action'] = $action;
				array_shift($matches); //remove first element
				$this->_debug['action_parameters'] = $matches;
				$initialValues = array();
				foreach (array('query', 'queryArgs', 'layout', 'view') as $property) {
					$initialValues[$property] = isset($this->$property) ? $this->$property : null;
				}
				try {
					$this->preDispatch($action, $matches);
					$this->dispatch($action, $matches);
					$this->postDispatch($action, $matches);
					$this->_debug['dispatch'] = 'success';
					//routed successfully
					return;
				} catch (RoutemasterException $e) {
					$this->_debug['dispatch_failures'][] = $e;
					//route failed so reset and continue routing
					$wp_query = new WP_Query();
//					$classVars = get_class_vars(get_called_class());
//					foreach ($classVars as $name => $value) {
//						if (in_array($name, array('query', 'queryArgs', 'layout', 'view'))) $this->$name = $value;
//					}
					foreach ($initialValues as $property=>$value) {
						$this->$property = $value;
					}
				}
			}
		}
		//no matched route
		$this->dispatch('show404');
	}

	protected function preDispatch($action, $args = array()) { /* do nothing by default */ }
	protected function postDispatch($action, $args = array()) { /* do nothing by default */ }

    public function dispatchVariables(){
        return array('view' => $this->view);
    }

	/**
	 * Runs an action and renders the view.
	 * @param $action string Action/method to run
	 * @param array $args URI parameters
	 */
	protected function dispatch($action, $args = array()) {
		//call action method
		call_user_func_array(array($this, $action), $args);

		//setup default view
		if (!isset($this->viewName)) $this->viewName = $action;

		//render view
		$viewFile = $this->viewPath . $this->viewName . ".php";
		$layoutFile = (empty($this->layout) ? null : $this->viewPath.$this->layout.".php");
		$this->view->render($viewFile, $layoutFile, $this->dispatchVariables());
	}

	/**
	 * Default 404 handler
	 */
	protected function show404() {
		header('HTTP/1.0 404 Not Found');
		if ($this->viewExists('404')) $this->viewName = '404';
		else die('404 File not found');
	}

	/**
	 * Tests if a view file exists in the view path
	 * @param $view string
	 * @return bool
	 */
	public function viewExists($view) {
		return file_exists($this->viewPath.$view.'.php');
	}

	/**
	 * Test config and set up hook for routing
	 */
	public function setup() {
		if (is_admin()) {
			//don't do any routing for admin pages
			return;
		} elseif (!get_option('permalink_structure')) {
			$url = admin_url('options-permalink.php');
			die("Permalinks must be <a href='$url'>enabled</a>.");
		} elseif (!defined('ROUTEMASTER')) {
			if (defined('WP_USE_THEMES') && WP_USE_THEMES) {
				//came in through index.php so show error
				die('Requests must be routed through index-rm.php');
			} else {
				//came in another way, such as wp-login.php, so stop routing but let WP continue
				return;
			}
		}

		//do routing once WP is fully loaded
		add_action('wp_loaded', array($this, 'route'));
	}
}