<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\controller;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\request\request;
use phpbb\user;

class like_post implements like_interface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $table_prefix;

	/**
	 * @var string
	 */
	protected $root_path;

	/**
	 * @var string
	 */
	protected $php_ext;

	/**
	 * Constructor.
	 */
	public function __construct( auth $auth, database $db, request $request, user $user, string $table_prefix, string $root_path, string $php_ext ) {

		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->table_prefix = $table_prefix;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

	}

	/**
	 * @todo
	 */
	public function like( $post_id ) {

		/**
		 * Fetch the post we like.
		 */
		$sql = 'SELECT * FROM ' . POSTS_TABLE . ' WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query( $sql );
		$post = $this->db->sql_fetchrow( $result );
		$this->db->sql_freeresult( $result );

		if ( ! $post ) {

			throw new \phpbb\exception\http_exception( 404, 'LIKE_POST_NOT_EXIST' );

		}

		/**
		 * Store important vars.
		 */
		$auth_id = (int) $this->user->data[ 'user_id' ];
		$post_id = (int) $post[ 'post_id' ];
		$post_author_id = (int) $post[ 'poster_id' ];

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
		 * Post already liked?
		 */
		$result = $this->db->sql_query(
 			'SELECT * FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $auth_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'1\''
 		);
 		$has_liked = $this->db->sql_fetchrow( $result );
 		$this->db->sql_freeresult( $result );

		/**
		 * Check if the user has liked this post already. If they have,
		 * then remove the like from the post. If they haven't, like it.
		 */
		if ( $has_liked ) {

			$sql = 'DELETE FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $auth_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'1\'';

			$this->db->sql_query( $sql );

			$this->db->sql_transaction( 'commit' );

		} else {

			$vars = [
				'user_id'			=> $auth_id,
				'post_author_id'	=> $post_author_id,
				'post_post_id'		=> $post_id,
				'type'				=> 1,
				'created_at'		=> date( 'Y-m-d H:i:s' )
			];

			$sql = 'INSERT INTO ' . $this->table_prefix . ' ' . $this->db->sql_build_array( 'INSERT', $vars );

			$this->db->sql_transaction( 'commit' );

		}

		die();

	}

}
