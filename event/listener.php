<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\event;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use danieltj\memberreputation\controller\functions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var helper
	 */
	protected $helper;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var functions
	 */
	protected $functions;

	/**
	 * Constructor.
	 */
	public function __construct( auth $auth, helper $helper, template $template, user $user, functions $functions ) {

		$this->auth = $auth;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->functions = $functions;

	}

	/**
	 * Hook into events.
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup'					=> 'add_languages',
			'core.permissions'					=> 'add_permissions',
			'core.viewtopic_modify_post_row'	=> 'topic_modify_post_row',
			'core.ucp_pm_view_message'			=> 'ucp_view_message',
			'core.memberlist_view_profile'		=> 'member_view_profile'
		];

	}

	/**
	 * Add languages.
	 */
	public function add_languages( $event ) {

		$lang_set_ext = $event[ 'lang_set_ext' ];

		$lang_set_ext[] = [
			'ext_name' => 'danieltj/memberreputation',
			'lang_set' => 'memberreputation',
		];

		$event[ 'lang_set_ext' ] = $lang_set_ext;

	}

	/**
	 * Add permissions.
	 */
	public function add_permissions( $event ) {

		$permissions = array_merge( $event[ 'permissions' ], [
			'u_can_like' 	=> [
				'lang'		=> 'ACL_U_CAN_LIKE',
				'cat'		=> 'post'
			],
			'u_can_dislike' => [
				'lang'		=> 'ACL_U_CAN_DISLIKE',
				'cat'		=> 'post'
			]
		] );

		$event[ 'permissions' ] = $permissions;

	}

	/**
	 * Add template vars to view topic.
	 */
	public function topic_modify_post_row( $event ) {

		/**
		 * Ignore guest posts.
		 */
		if ( ANONYMOUS == $event[ 'row' ][ 'post_id' ] ) {

			return;

		}

		/**
		 * Store important vars.
		 */
		$auth_id = (int) $this->user->data[ 'user_id' ];
		$post_id = (int) $event[ 'row' ][ 'post_id' ];
		$post_author_id = (int) $event[ 'row' ][ 'user_id' ];

		/**
		 * Fetch like status for this post.
		 */
		$likes = $this->functions->has_liked_post( $auth_id, $post_id );

		/**
		 * Fetch dislike status for this post.
		 */
		$dislikes = $this->functions->has_disliked_post( $auth_id, $post_id );

		/**
		 * Fetch the post author's reputation.
		 */
		$user_total_rep = $this->functions->user_rep_score( $post_author_id );

		/**
		 * Create link & dislike URLs.
		 */
		$like_post_url = $this->helper->route( 'danieltj_memberreputation_like_post_controller', [
			'post_id' => $post_id, 'hash' => generate_link_hash( 'like_post' )
		] );

		$dislike_post_url = $this->helper->route( 'danieltj_memberreputation_dislike_post_controller', [
			'post_id' => $post_id, 'hash' => generate_link_hash( 'dislike_post' )
		] );

		/**
		 * Merge our template vars.
		 */
		$event[ 'post_row' ] = array_merge( $event[ 'post_row' ], [
			'U_CAN_LIKE'			=> ( ANONYMOUS !== (int) $post_author_id && $this->auth->acl_get( 'u_can_like' ) ) ? true : false,
			'U_CAN_DISLIKE'			=> ( ANONYMOUS !== (int) $post_author_id && $this->auth->acl_get( 'u_can_dislike' ) ) ? true : false,
			'U_CAN_LIKE_POST'		=> ( $auth_id !== $post_author_id ) ? true : false,
			'U_CAN_DISLIKE_POST'	=> ( $auth_id !== $post_author_id ) ? true : false,
			'U_HAS_LIKED_POST'		=> ( $likes ) ? true : false,
			'U_HAS_DISLIKED_POST'	=> ( $dislikes ) ? true : false,
			'U_LIKE_POST_URL'		=> $like_post_url,
			'U_DISLIKE_POST_URL'	=> $dislike_post_url,
			'USER_HAS_NO_REP'		=> ( ANONYMOUS === (int) $post_author_id ) ? true : false,
			'USER_TOTAL_REP'		=> $user_total_rep
		] );

	}

	/**
	 * Add template vars to view PM.
	 */
	public function ucp_view_message( $event ) {

		/**
		 * Capture the user id.
		 */
		$user_id = $event[ 'user_info' ][ 'user_id' ];

		/**
		 * Fetch the post author's reputation.
		 */
		$user_total_rep = $this->functions->user_rep_score( $user_id );

		/**
		 * Merge our template vars.
		 */
		 $this->template->assign_vars( [
			'USER_HAS_NO_REP'	=> ( ANONYMOUS === (int) $user_id ) ? true : false,
 			'USER_TOTAL_REP'	=> $user_total_rep
 		] );

	}

	/**
	 * Add template vars to profile.
	 */
	public function member_view_profile( $event ) {

		/**
		 * Capture the user id.
		 */
		$user_id = $event[ 'member' ][ 'user_id' ];

		/**
		 * Fetch the post author's reputation.
		 */
		$user_total_rep = $this->functions->user_rep_score( $user_id );

		/**
		 * Merge our template vars.
		 */
		 $this->template->assign_vars( [
 			'USER_TOTAL_REP' => $user_total_rep
 		] );

	}

}
