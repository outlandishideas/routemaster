<?php

namespace Outlandish\Wordpress\Routemaster\Oowp;

use Outlandish\Wordpress\Routemaster\Model\Route;
use Outlandish\Wordpress\Routemaster\Response\XmlResponse;
use Outlandish\Wordpress\Routemaster\Router;

/**
 * An OOWP-aware router
 */
abstract class OowpRouter extends Router {

	/**
	 * Default routes
	 * Routes are tested in descending order
	 * @var array Map of regular expressions to method names
	 */
	protected $defaultRoutes = array(
		'|^sitemap.xml$|i' => 'sitemap', //xml sitemap for google etc
		'|^robots.txt$|' => 'robots',
		'|([^/]+)/?$|' => 'defaultPost', //matches blah/blah/slug
		'|^$|' => 'frontPage' //matches empty string
	);
	/** @var OowpRouterHelper */
	protected $helper;

	protected function __construct($helper = null) {
		parent::__construct($helper ?: new OowpRouterHelper());
		add_filter('post_type_link', array($this, 'permalinkHook'), 10, 4);
	}

	/** @var null Used in permalinkHook function, to prevent infinite recursion */
	protected $permalinkHookPostId = null;

	/**
	 * Overwrites the post_link with the post's permalink()
	 * @param $post_link
	 * @param $post
	 * @param $leavename
	 * @param $sample
	 * @return string|void
	 */
	public function permalinkHook($post_link, $post, $leavename, $sample) {
		if ($post->post_name && $post->ID != $this->permalinkHookPostId) {
			// prevent infinite recursion by saving the ID before calling permalink() (which may come back here again)
			$this->permalinkHookPostId = $post->ID;
			$post_link = \ooPost::createPostObject($post)->permalink($leavename);
			$this->permalinkHookPostId = null;
		}
		return $post_link;
	}

	/**
	 * Concatenates the routes in $this->routes with the default routes
	 * @return Route[]
	 */
	protected function getRoutes(){
		$routes = [];
		if(!empty($this->routes) && is_array($this->routes)){
			$routes = $this->routes;
		}
		foreach ($this->defaultRoutes as $path => $action) {
			$routes[] = new Route($path, $action, $this);
		}
		return $routes;
	}

	/***********************************************
	 *
	 *  Methods for default routes (defined above)
	 *
	 ***********************************************/

	/**
	 * @route /sitemap.xml
	 */
	protected function sitemap() {
		return new XmlResponse([
			'pageItems' => new \ooWP_Query(array('post_type' => 'any', 'orderby' => 'date'))
		]);
	}

	/**
	 * @route /robots.txt
	 */
	protected function robots() {
		do_action('do_robots');
		exit;
	}

	/**
	 * @route /any/unknown/route
	 */
	protected function show404() {
		global $post;
		$post = new \ooFakePost(array('post_title' => 'Page not found'));
		$response = parent::show404();
		$response->outputArgs['theme'] = \ooTheme::getInstance();
		return $response;
	}

	/**
	 * @route /default/route/when/no/other/match
	 */
	protected function defaultPost($slug) {
		$post = $this->helper->querySingle(array('name' => $slug, 'post_type' => 'any'), true);

		$response = $this->helper->createDefaultResponse([
			'post' => $post
		]);
		if ($post->post_type == 'page' && $response->viewExists('page-' . $post->post_name)) {
			$response->viewName = 'page-' . $post->post_name;
		}
		return $response;
	}

	/**
	 * @route /
	 */
	protected function frontPage() {
		return [
			'post' => $this->helper->querySingle(array('page_id' => get_option('page_on_front')), true)
		];
	}

}