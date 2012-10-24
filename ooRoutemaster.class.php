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
		'|([^/]+)/?$|' => 'defaultPost', //matches blah/blah/slug
		'|^$|' => 'frontPage' //matches empty string
	);

    public function dispatchVariables(){
        return array('view' => $this->view, '_theme' => ooTheme::getInstance());
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
		$this->layout = false;
		$this->view->pageItems = ooPost::fetchAll(array('post_type' => 'any', 'orderby' => 'date'	));

	}


	protected function show404() {
		global $post;
		$post = ooPost::makeFake(array('post_title' => 'Page not found'));
		parent::show404();
	}

	/* Helper methods */

	/**
	 * Check that the requested URI matches the post permalink and redirect if not
	 * @param $post
	 */
	protected function redirectCanonical($post) {
		if (trim(get_bloginfo('url') . '/' . $this->requestUri, ' /') != trim($post->permalink(), ' /')) {
			wp_redirect($post->permalink());
		}
	}

	/**
	 * Create a new query object and set the global $wp_query
	 * @param $args
	 * @return ooWP_Query
	 */
	protected function query($args) {
		global $wp_query;
		$wp_query = ooPost::fetchAll($args);
		return $wp_query;
	}

	/**
	 * Select a single post, set globals and throw 404 exception if nothing matches
	 * @param $args
	 * @return ooPost
	 * @throws RoutemasterException
	 */
	protected function querySingle($args) {
		global $post;
		$query = $this->query($args);
		//no matched posts so 404
		if (!count($query)) throw new RoutemasterException('Not found', 404);

		$post = $query[0];

		return $post;
	}

	/***********************************************
	 *
	 *  Methods for default routes (defined above)
	 *
	 ***********************************************/

	protected function defaultPost($slug) {
		$post = $this->querySingle(array('name' => $slug, 'post_type' => 'any'));

		if ($post->post_type == 'page') {
			if ($this->viewExists('page-' . $post->post_name)) {
				$this->viewName = 'page-' . $post->post_name;
			}
		}
	}

	protected function frontPage() {
		$this->querySingle(array('page_id' => get_option('page_on_front')));
	}



}