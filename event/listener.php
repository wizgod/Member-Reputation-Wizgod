<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\event;

use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/**
	 * @var helper
	 */
	protected $helper;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var user
	 */
	protected $user;

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
	public function __construct( helper $helper, language $language, request $request, template $template, user $user, string $root_path, string $php_ext ) {

		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

	}

	/**
	 * Hook into events.
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup'					=> 'add_languages',
			'core.permissions'					=> 'add_permissions'
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

}
