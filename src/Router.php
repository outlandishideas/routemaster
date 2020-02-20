<?php

namespace Outlandish\Wordpress\Routemaster;

use Outlandish\Wordpress\Routemaster\Exception\RoutemasterException;
use Outlandish\Wordpress\Routemaster\Model\Route;
use Outlandish\Wordpress\Routemaster\Response\RobotsResponse;
use Outlandish\Wordpress\Routemaster\Response\RoutemasterResponse;

/**
 * Base Routing/Controller/View class. Extend this in your theme.
 */

/** @property RouterHelper $helper */
abstract class Router
{
    private static $instance;

    /** @var Route[] */
    protected $routes;

    protected $requestUri;
    protected $_debug;

    protected $helper;

    protected function __construct($helper = null)
    {
    	$this->helper = $helper ?: new RouterHelper();

		remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'feed_links_extra', 3);

        //remove these built-in WP actions
        remove_action('template_redirect', 'wp_old_slug_redirect');
        remove_action('template_redirect', 'redirect_canonical');
    }

	/**
	 * Initialise the router
	 */
	public function setup()
	{
		if (is_admin() || !defined('WP_USE_THEMES')) {
			//don't do any routing for admin pages
			return;
		}elseif (!get_option('permalink_structure')) {
			$url = admin_url('options-permalink.php');
			die("Permalinks must be <a href='$url'>enabled</a>.");
		}

		//do routing once WP is fully loaded
		add_action('wp_loaded', function() {
			$this->route();
		});
	}

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    public function addRoute($pattern, $action, $handler = null)
    {
        if (!$this->routes) {
            $this->routes = [];
        }
        $this->routes[] = new Route($pattern, $action, $handler ?: $this);
    }

	/**
	 * @static
	 * @return Router Singleton instance
	 */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

	/**
	 * Gets default routes
	 * Routes are tested in descending order
	 * @return array Map of regular expressions to method names
	 */
	protected function getDefaultRoutePatterns()
	{
		return [
			'|^robots.txt$|' => 'robots',
		];
	}

	/**
	 * Concatenates the routes in $this->routes with any default route patterns
	 * @return Route[]
	 */
	protected function buildRoutes(){
		$routes = [];
		if(!empty($this->routes) && is_array($this->routes)){
			$routes = $this->routes;
		}
		foreach ($this->getDefaultRoutePatterns() as $path => $action) {
			$routes[] = new Route($path, $action, $this);
		}
		return $routes;
	}

	/**
     * Main workhorse method.
     * Attempts to match URI against routes and dispatches routing methods.
     * Falls back to 404 if none match.
     */
    public function route()
    {
        global $wp_query;

        if ( ! empty($_REQUEST['preview'])) {
            //adding support for preview. PROSPECT-526
            $requestUri = $_SERVER['REQUEST_URI'];
        } else {
            //strip base dir and query string from request URI
            $base       = dirname($_SERVER['SCRIPT_NAME']);
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $requestUri = preg_replace("|^$base/?|", '', $requestUri);
            $requestUri = ltrim($requestUri, '/'); //ensure left-leading "/" is stripped.
        }

        $isJsonRequest = strpos($requestUri, 'wp-json') === 0;

	    if ($isJsonRequest) {
		    //don't do any routing wp-json API requests
		    return;
	    }

		$allRoutes = $this->buildRoutes();

        $this->requestUri = $requestUri;
        $this->_debug = [
			'routes' => $allRoutes,
			'requestUri' => $this->requestUri
		];

        //find matching route(s)
		$matchingRoutes = [];
		foreach ($allRoutes as $route) {
			if (preg_match($route->pattern, $this->requestUri, $matches)) {
				array_shift($matches); //remove first element
				$matchingRoutes[] = [
					'route' => $route,
					'matches' => $matches
				];
			}
		}

		$handled = false;
        foreach ($matchingRoutes as $match) {
        	/** @var Route $route */
        	$route = $match['route'];
        	$matches = $match['matches'];

			$this->_debug['matched_route'] = $route->pattern;
			$this->_debug['matched_action'] = $route->actionName;
			$this->_debug['matched_handler'] = $route->handler;
			$this->_debug['action_parameters'] = $matches;

			try {
				$this->dispatch($route->handler, $route->actionName, $matches);
				$handled = true;
			} catch (RoutemasterException $e) {
				if (!isset($this->_debug['dispatch_failures'])) {
					$this->_debug['dispatch_failures'] = [];
				}
				$this->_debug['dispatch_failures'][] = $e;

				if ($e->allowFallback) {
					//route failed so reset and continue routing
					$wp_query->init();
				} else {
					$this->dispatch($this, 'show404');
					$handled = true;
					break;
				}
			}
        }

        if (!$handled) {
			//no matched route
			$wp_query->is_404 = true;
			$this->dispatch($this, 'show404');
		}
    }

	/**
	 * Runs an action and renders the view.
	 * @param $handler
	 * @param string $actionName Action/method to run
	 * @param array $requestArgs URI parameters
	 */
    public function dispatch($handler, $actionName, $requestArgs = array())
    {
		//call action method
		try {
			$response = call_user_func_array(array($handler, $actionName), $requestArgs);
		} catch (RoutemasterException $ex) {
			if ($ex->response) {
				$response = $ex->response;
			} else {
				throw $ex;
			}
		}

		//allow plugins to hook in after $wp_query is set but before view is rendered
		do_action('template_redirect');

		if (!$response || !($response instanceof RoutemasterResponse)) {
			$response = $this->helper->createDefaultResponse($response);
		}

		$response->setRouteName($actionName);
		$response->handleRequest();
    }

	/**
	 * Default 404 handler
	 */
    protected function show404()
    {
		$response = $this->helper->createNotFoundResponse();
		$response->setRouteName('404');
		$response->headers[] = 'HTTP/1.0 404 Not Found';
		return $response;
    }

	/**
	 * @route /robots.txt
	 */
	protected function robots() {
		return new RobotsResponse();
	}
}
