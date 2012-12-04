<?php

/**
 * Base Routing/Controller/View class. Extend this in your theme.
 */
abstract class ooRoutemaster extends Routemaster {

	/** Default routes
	 * Routes are tested in descending order
	 * @var array Map of regular expressions to method names
	 */
	static protected $defaultRoutes = array(
		'|^sitemap.xml$|i' => 'sitemap', //xml sitemap for google etc
		'|^robots.txt$|' => 'robots',
		'|([^/]+)/?$|' => 'defaultPost', //matches blah/blah/slug
		'|^$|' => 'frontPage' //matches empty string
	);

	protected function __construct() {
		parent::__construct();
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
			$post_link = ooPost::createPostObject($post)->permalink();
			$this->permalinkHookPostId = null;
		}
		return $post_link;
	}

	/**
	 * @var array
	 */
	protected $routes;

	protected function routes(){
		if(!empty($this->routes) && is_array($this->routes)){
			return array_merge($this->routes, ooRoutemaster::$defaultRoutes);
		}
		return ooRoutemaster::$defaultRoutes;
	}

	protected function sitemap() {
		header('Content-Type: application/xml');
		$this->layout = false;
		$this->view->logDebug = false;
		$this->view->pageItems = new ooWP_Query(array('post_type' => 'any', 'orderby' => 'date'));
	}

	protected function robots() {
		do_action('do_robots');
		exit;
	}

	protected function show404() {
		global $post;
		$post = new ooFakePost(array('post_title' => 'Page not found'));
		parent::show404();
	}

	/* Helper methods */

	/**
	 * Check that the requested URI matches the post permalink and redirect if not
	 * @param ooPost $post
	 */
	protected function redirectCanonical($post) {
		$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if ("$scheme://$_SERVER[HTTP_HOST]$path" != $post->permalink()) {
			wp_redirect($post->permalink());
			die;
		}
	}

	/**
	 * Create a new query object and set the global $wp_query
	 * @param $args
	 * @return ooWP_Query
	 */
	protected function query($args) {
		global $wp_query;
		$wp_query = new ooWP_Query($args);
		return $wp_query;
	}

	/**
	 * Select a single post, set globals and throw 404 exception if nothing matches
	 * @param $args
	 * @param bool $redirectCanonical true if should redirect canonically after fetching the post
	 * @throws RoutemasterException
	 * @return ooPost
	 */
	protected function querySingle($args, $redirectCanonical = false) {
		global $post;
		$query = $this->query($args);
		//no matched posts so 404
		if (!count($query)) throw new RoutemasterException('Not found', 404);

		$post = $query[0];

		if ($redirectCanonical) {
			$this->redirectCanonical($post);
		}

		return $post;
	}

	/***********************************************
	 *
	 *  Methods for default routes (defined above)
	 *
	 ***********************************************/

	protected function defaultPost($slug) {
		$post = $this->querySingle(array('name' => $slug, 'post_type' => 'any'), true);

		if ($post->post_type == 'page') {
			if ($this->viewExists('page-' . $post->post_name)) {
				$this->viewName = 'page-' . $post->post_name;
			}
		}
	}

	protected function frontPage() {
		$this->querySingle(array('page_id' => get_option('page_on_front')), true);
	}



}