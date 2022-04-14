<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\controller;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\user;

class dislike_post implements dislike_interface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var language
	 */
	protected $language;

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
	public function __construct( auth $auth, database $db, language $language, request $request, user $user, string $table_prefix, string $root_path, string $php_ext ) {

		$this->auth = $auth;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->user = $user;
		$this->table_prefix = $table_prefix;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

	}

	/**
	 * @todo
	 */
	public function dislike( $post_id ) {

		/**
		 * Fetch the post we dislike.
		 */
		$sql = 'SELECT * FROM ' . POSTS_TABLE . ' WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query( $sql );
		$post = $this->db->sql_fetchrow( $result );
		$this->db->sql_freeresult( $result );

		if ( ! $post ) {

			throw new \phpbb\exception\http_exception( 404, 'CANNOT_DISLIKE_NO_EXIST_POST' );

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
		if ( ! check_link_hash( $this->request->variable( 'hash', '' ), 'dislike_post' ) ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_PASS_BAD_CSRF_TOKEN' );

		}

		/**
		 * Don't support Ajax right now, sorry.
		 */
		if ( $this->request->is_ajax() ) {

			throw new \phpbb\exception\http_exception( 500, 'GENERAL_ERROR' );

		}

		if ( ANONYMOUS === $post_author_id ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_DISLIKE_GUEST_POSTS' );

		}

		if ( ! $this->auth->acl_get( 'u_can_dislike' ) ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_DISLIKE_ANY_POSTS' );

		}

		if ( $auth_id === $post_author_id ) {

			throw new \phpbb\exception\http_exception( 500, 'CANNOT_DISLIKE_OWN_POSTS' );

		}

		/**
		 * Post already disliked?
		 */
		$result = $this->db->sql_query(
 			'SELECT * FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $auth_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'0\''
 		);
 		$has_disliked = $this->db->sql_fetchrow( $result );
 		$this->db->sql_freeresult( $result );

		/**
		 * Check if the user has disliked this post already. If they have,
		 * then remove the dislike from the post. If they haven't, dislike it.
		 */
		if ( $has_disliked ) {

			$sql = 'DELETE FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $auth_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'0\'';
			$this->db->sql_query( $sql );
			$this->db->sql_transaction( 'commit' );

		} else {

			$vars = [
				'user_id'			=> $auth_id,
				'post_author_id'	=> $post_author_id,
				'post_post_id'		=> $post_id,
				'type'				=> 0,
				'created_at'		=> date( 'Y-m-d H:i:s' )
			];

			$sql = 'INSERT INTO ' . $this->table_prefix . 'reputation ' . $this->db->sql_build_array( 'INSERT', $vars );
			$this->db->sql_query( $sql );
			$this->db->sql_transaction( 'commit' );

		}

		/**
		 * Go back to the post.
		 */
		redirect( append_sid( 'viewtopic.' . $this->php_ext . '?p=' . $post_id . '#p' . $post_id ) );

	}

}
