<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\controller;

use phpbb\auth\auth;
use phpbb\request\request;
use phpbb\user;
use danieltj\memberreputation\core\functions;

class like_post implements like_interface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var functions
	 */
	protected $functions;

	/**
	 * @var string
	 */
	protected $php_ext;

	/**
	 * Constructor.
	 */
	public function __construct( auth $auth, request $request, user $user, functions $functions, string $php_ext ) {

		$this->auth = $auth;
		$this->request = $request;
		$this->user = $user;
		$this->functions = $functions;
		$this->php_ext = $php_ext;

	}

	/**
	 * Handle the like endpoint.
	 */
	public function like( $post_id ) {

		/**
		 * Fetch the post we like.
		 */
		$post = $this->functions->get_post( $post_id );

 		if ( false === $post ) {

			throw new \phpbb\exception\http_exception( 404, 'CANNOT_LIKE_NO_EXIST_POST' );

		}

		/**
		 * Store important vars.
		 */
		$auth_id = (int) $this->user->data[ 'user_id' ];
		$post_id = (int) $post[ 'post_id' ];
		$post_author_id = (int) $post[ 'poster_id' ];

		/**
		 * CSRF token check.
		 */
		if ( ! check_link_hash( $this->request->variable( 'hash', '' ), 'like_post' ) ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_PASS_BAD_CSRF_TOKEN' );

		}

		/**
		 * Don't support Ajax right now, sorry.
		 */
		if ( $this->request->is_ajax() ) {

			throw new \phpbb\exception\http_exception( 500, 'GENERAL_ERROR' );

		}

		if ( ANONYMOUS === $post_author_id ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_LIKE_GUEST_POSTS' );

		}

		if ( ! $this->auth->acl_get( 'u_can_like' ) ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_LIKE_ANY_POSTS' );

		}

		if ( $auth_id === $post_author_id ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_LIKE_OWN_POSTS' );

		}

		/**
		 * Check if the user has liked this post already. If they have,
		 * then remove the like from the post. If they haven't, like it.
		 */
		if ( $this->functions->has_liked_post( $auth_id, $post_id ) ) {

			$like_post = $this->functions->remove_like( $auth_id, $post_id );

		} else {

			$like_post = $this->functions->like_post( $auth_id, $post_author_id, $post_id );

		}

		/**
		 * Go back to the post.
		 */
		redirect( append_sid( 'viewtopic.' . $this->php_ext . '?p=' . $post_id . '#p' . $post_id ) );

	}

}
