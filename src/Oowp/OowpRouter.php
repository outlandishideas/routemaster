<?php

namespace Outlandish\Wordpress\Routemaster\Oowp;

use Outlandish\Wordpress\Oowp\OowpQuery;
use Outlandish\Wordpress\Oowp\PostTypes\FakePost;
use Outlandish\Wordpress\Oowp\PostTypes\WordpressPost;
use Outlandish\Wordpress\Routemaster\Response\TemplatedResponse;
use Outlandish\Wordpress\Routemaster\Response\XmlResponse;
use Outlandish\Wordpress\Routemaster\Router;

/**
 * An OOWP-aware router
 */
/** @property OowpRouterHelper $helper */
abstract class OowpRouter extends Router {

	protected function __construct($helper = null) {
		parent::__construct($helper ?: new OowpRouterHelper());
		add_filter('post_type_link', function($postLink, $post, $leavename, $sample) {
			return $this->permalinkHook($postLink, $post);
		}, 10, 4);
	}

	protected function getDefaultRoutePatterns()
	{
		return array_merge(parent::getDefaultRoutePatterns(), [
			'|^sitemap.xml$|i' => 'sitemap', //xml sitemap for google etc
			'|([^/]+)/?$|' => 'defaultPost', //matches blah/blah/slug
			'|^$|' => 'frontPage' //matches empty string
		]);
	}


	/** @var null Used in permalinkHook function, to prevent infinite recursion */
	protected $permalinkHookPostId = null;

	/**
	 * Overwrites the post_link with the post's permalink()
	 * @param $post_link
	 * @param $post
	 * @return string|void
	 */
	public function permalinkHook($post_link, $post) {
		if ($post->post_name && $post->ID != $this->permalinkHookPostId) {
			// prevent infinite recursion by saving the ID before calling permalink() (which may come back here again)
			$this->permalinkHookPostId = $post->ID;
			$post_link = WordpressPost::createWordpressPost($post)->permalink();
			$this->permalinkHookPostId = null;
		}
		return $post_link;
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
			'pageItems' => new OowpQuery(array('post_type' => 'any', 'orderby' => 'date'))
		]);
	}

	/**
	 * @route /any/unknown/route
	 */
	protected function show404() {
		global $post;
		$post = new FakePost(array('post_title' => 'Page not found'));
		return parent::show404();
	}

	/**
	 * @route /default/route/when/no/other/match
	 */
	protected function defaultPost($slug) {
		$post = $this->helper->querySingle(array('name' => $slug, 'post_type' => 'any'), true);

		$response = $this->helper->createDefaultResponse([
			'post' => $post
		]);
		if ($response instanceof TemplatedResponse && $post->post_type == 'page' && $response->viewExists('page-' . $post->post_name)) {
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