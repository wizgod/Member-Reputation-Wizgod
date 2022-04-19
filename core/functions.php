<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\core;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\user;

final class functions {

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
	 * Fetch a post.
	 *
	 * @param integer $user_id The post ID of the post to fetch.
	 *
	 * @return array|boolean The array of post data or false if it doesn't exist.
	 */
	public function get_post( $post_id = 0 ) {

		$result = $this->db->sql_query(
			'SELECT * FROM ' . POSTS_TABLE . ' WHERE post_id = ' . (int) $post_id
		);

		$post = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		if ( $post ) {

			return $post;

		}

		return false;

	}

	/**
	 * Fetch the number of post likes.
	 *
	 * @param integer $post_id The post ID to get likes for.
	 *
	 * @return array|boolean The like data or false if none.
	 */
	public function post_like_count( $post_id = 0 ) {

		$result = $this->db->sql_query(
 			'SELECT COUNT(*) FROM ' . $this->table_prefix . 'reputation WHERE post_post_id = \'' . $post_id . '\' AND type = \'1\''
 		);

 		$likes = $this->db->sql_fetchrow( $result );

 		$this->db->sql_freeresult( $result );

		if ( $likes && isset( $likes[ 'COUNT(*)' ] ) ) {

			return (int) $likes[ 'COUNT(*)' ];

		}

		return false;

	}

	/**
	 * Fetch the number of post dislikes.
	 *
	 * @param integer $post_id The post ID to get dislikes for.
	 *
	 * @return array|boolean The dislikes data or false if none.
	 */
	public function post_dislike_count( $post_id = 0 ) {

		$result = $this->db->sql_query(
 			'SELECT COUNT(*) FROM ' . $this->table_prefix . 'reputation WHERE post_post_id = \'' . $post_id . '\' AND type = \'0\''
 		);

 		$dislikes = $this->db->sql_fetchrow( $result );

 		$this->db->sql_freeresult( $result );

		if ( $dislikes && isset( $dislikes[ 'COUNT(*)' ] ) ) {

			return (int) $dislikes[ 'COUNT(*)' ];

		}

		return false;

	}

	/**
	 * Checks if a user likes a post.
	 *
	 * @param integer $user_id The user ID of who (maybe) liked the post.
	 * @param integer $post_id The post ID that was (maybe) liked.
	 *
	 * @return boolean
	 */
	public function has_liked_post( $user_id = 0, $post_id = 0 ) {

		$result = $this->db->sql_query(
 			'SELECT * FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $user_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'1\''
 		);

 		$likes = $this->db->sql_fetchrow( $result );

 		$this->db->sql_freeresult( $result );

		if ( $likes ) {

			return true;

		}

		return false;

	}

	/**
	 * Checks if a user dislikes a post.
	 *
	 * @param integer $user_id The user ID of who (maybe) disliked the post.
	 * @param integer $post_id The post ID that was (maybe) disliked.
	 *
	 * @return boolean
	 */
	public function has_disliked_post( $user_id = 0, $post_id = 0 ) {

		$result = $this->db->sql_query(
 			'SELECT * FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $user_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'0\''
 		);

 		$dislikes = $this->db->sql_fetchrow( $result );

 		$this->db->sql_freeresult( $result );

		if ( $dislikes ) {

			return true;

		}

		return false;

	}

	/**
	 * Like a post.
	 *
	 * @param integer $user_id			The user ID of who likes the post.
	 * @param integer $post_author_id	The user ID of the post author.
	 * @param integer $post_id			The post ID that was liked.
	 *
	 * @return boolean Always returns true.
	 */
	public function like_post( $user_id = 0, $post_author_id = 0, $post_id = 0 ) {

		$sql = 'INSERT INTO ' . $this->table_prefix . 'reputation ' . $this->db->sql_build_array( 'INSERT', [
			'user_id'			=> $user_id,
			'post_author_id'	=> $post_author_id,
			'post_post_id'		=> $post_id,
			'type'				=> 1,
			'created_at'		=> date( 'Y-m-d H:i:s' )
		] );

		$this->db->sql_query( $sql );

		$this->db->sql_transaction( 'commit' );

		return true;

	}

	/**
	 * Unlike a post.
	 *
	 * @param integer $user_id The user ID of who is removing their like.
	 * @param integer $post_id The post ID that is no longer disliked.
	 *
	 * @return boolean Always returns true.
	 */
	public function remove_like( $user_id = 0, $post_id = 0 ) {

		$this->db->sql_query(
			'DELETE FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $user_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'1\''
		);

		$this->db->sql_transaction( 'commit' );

		return true;

	}

	/**
	 * Dislike a post.
	 *
	 * @param integer $user_id			The user ID of who dislikes the post.
	 * @param integer $post_author_id	The user ID of the post author.
	 * @param integer $post_id			The post ID that was disliked.
	 *
	 * @return boolean Always returns true.
	 */
	public function dislike_post( $user_id = 0, $post_author_id = 0, $post_id = 0 ) {

		$sql = 'INSERT INTO ' . $this->table_prefix . 'reputation ' . $this->db->sql_build_array( 'INSERT', [
			'user_id'			=> $user_id,
			'post_author_id'	=> $post_author_id,
			'post_post_id'		=> $post_id,
			'type'				=> 0,
			'created_at'		=> date( 'Y-m-d H:i:s' )
		] );

		$this->db->sql_query( $sql );

		$this->db->sql_transaction( 'commit' );

		return true;

	}

	/**
	 * Undislike a post. Is undislike even a word?
	 *
	 * @param integer $user_id The user ID of who is removing their dislike.
	 * @param integer $post_id The post ID that is no longer disliked.
	 *
	 * @return boolean Always returns true.
	 */
	public function remove_dislike( $user_id = 0, $post_id = 0 ) {

		$this->db->sql_query(
			'DELETE FROM ' . $this->table_prefix . 'reputation WHERE user_id = \'' . $user_id . '\' AND post_post_id = \'' . $post_id . '\' AND type = \'0\''
		);

		$this->db->sql_transaction( 'commit' );

		return true;

	}

	/**
	 * Return a user's reputation score.
	 *
	 * @param integer $user_id The user ID who's rep score we want.
	 *
	 * @return integer
	 */
	public function user_rep_score( $user_id = 0 ) {

		/**
		 * Count the user's liked posts.
		 */
		$result = $this->db->sql_query(
 			'SELECT COUNT(*) FROM ' . $this->table_prefix . 'reputation WHERE post_author_id = \'' . $user_id . '\' AND type = \'1\''
 		);

		$user_liked = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		/**
		 * Count the user's disliked posts.
		 */
		$result = $this->db->sql_query(
 			'SELECT COUNT(*) FROM ' . $this->table_prefix . 'reputation WHERE post_author_id = \'' . $user_id . '\' AND type = \'0\''
 		);

		$user_disliked = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		return intval( $user_liked[ 'COUNT(*)' ] ) - intval( $user_disliked[ 'COUNT(*)' ] );

	}

}
