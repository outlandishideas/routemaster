<?php

namespace Outlandish\Wordpress\Routemaster\Oowp;

use Outlandish\Wordpress\Routemaster\RoutemasterException;
use Outlandish\Wordpress\Routemaster\RouterHelper;

/**
 * An OOWP-aware router helper
 */
class OowpRouterHelper extends RouterHelper
{
	/**
	 * Check that the requested URI matches the post permalink and redirect if not
	 * @param \ooPost $post
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
	 * @return \ooWP_Query
	 */
	protected function query($args) {
		global $wp_query, $wp_the_query;
		$wp_the_query = $wp_query = new \ooWP_Query($args);
		return $wp_query;
	}

	/**
	 * Select a single post, set globals and throw 404 exception if nothing matches
	 * @param $args
	 * @param bool $redirectCanonical true if should redirect canonically after fetching the post
	 * @throws RoutemasterException
	 * @return \ooPost
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