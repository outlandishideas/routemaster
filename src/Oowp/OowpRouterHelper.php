<?php

namespace Outlandish\Wordpress\Routemaster\Oowp;

use Outlandish\Wordpress\Oowp\OowpQuery;
use Outlandish\Wordpress\Oowp\PostTypes\WordpressPost;
use Outlandish\Wordpress\Oowp\Views\OowpView;
use Outlandish\Wordpress\Routemaster\Exception\RoutemasterException;
use Outlandish\Wordpress\Routemaster\Oowp\Response\ContainerViewResponse;
use Outlandish\Wordpress\Routemaster\Oowp\View\NotFoundView;
use Outlandish\Wordpress\Routemaster\Response\HtmlResponse;
use Outlandish\Wordpress\Routemaster\RouterHelper;

/**
 * An OOWP-aware router helper
 */
class OowpRouterHelper extends RouterHelper
{
	/**
	 * Routes use this when creating a response
	 * @param array|object $args
	 * @return ContainerViewResponse|HtmlResponse
	 */
	public function createDefaultResponse($args = [])
	{
		if ($args instanceof OowpView) {
			return new ContainerViewResponse($args);
		} else {
			return parent::createDefaultResponse($args);
		}
	}

	public function createNotFoundResponse()
	{
		return new ContainerViewResponse(new NotFoundView());
	}

	/**
	 * Check that the requested URI matches the post permalink and redirect if not
	 * @param WordpressPost $post
	 */
	protected function redirectCanonical($post) {
		$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$permalink = $post->permalink();
		if ("$scheme://$_SERVER[HTTP_HOST]$path" != $permalink) {
			wp_redirect($permalink);
			die;
		}
	}

	/**
	 * Create a new query object and set the global $wp_query
	 * @param $args
	 * @return OowpQuery
	 */
	protected function query($args) {
		global $wp_query, $wp_the_query;
		$wp_the_query = $wp_query = new OowpQuery($args);
		return $wp_query;
	}

	/**
	 * Select a single post, set globals and throw 404 exception if nothing matches
	 * @param array $args
	 * @param bool $redirectCanonical true if should redirect canonically after fetching the post
	 * @throws RoutemasterException
	 * @return WordpressPost
	 */
	public function querySingle($args, $redirectCanonical = false) {
		global $post;

		if (isset($_GET['preview']) && $_GET['preview'] == 'true') {
			//currently published posts just need this to show the latest autosave instead
			$args['preview'] = 'true';

			//for unpublished posts, override query entirely
			if (isset($_GET['p']) || isset($_GET['page_id'])) {
				$args = array_intersect_key($_GET, array_flip(array('preview', 'p', 'page_id')));
			}

			//for unpublished posts and posts returned to draft, allow draft status
			$args['post_status'] = array('draft', 'publish', 'auto-draft');

			$redirectCanonical = false;
		}

		$query = $this->query($args);
		//no matched posts so 404
		if (!count($query)) {
			throw new RoutemasterException('Not found', 404);
		}

		$post = $query[0];

		if ($redirectCanonical) {
			$this->redirectCanonical($post);
		}

		return $post;
	}
}