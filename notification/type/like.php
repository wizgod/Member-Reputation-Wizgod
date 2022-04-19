<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\notification\type;

class like extends \phpbb\notification\type\base {

	/**
	 * @var \phpbb\controller\helper
	 */
	protected $helper;

	/**
	 * @var \phpbb\user_loader
	 */
	protected $user_loader;

	/**
	 * Set the controller helper.
	 *
	 * @param \phpbb\controller\helper $helper
	 *
	 * @return void
	 */
	public function set_controller_helper( \phpbb\controller\helper $helper ) {

		$this->helper = $helper;

	}

	/**
	 * Set the user loader.
	 *
	 * @param \phpbb\user_loader $user_loader
	 *
	 * @return void
	 */
	public function set_user_loader( \phpbb\user_loader $user_loader ) {

		$this->user_loader = $user_loader;

	}

	/**
	 * Return the notification name.
	 *
	 * @return string
	 */
	public function get_type() {

		return 'danieltj.memberreputation.notification.type.like';

	}

	/**
	 * Can this be managed in the UCP Notification Centre?
	 *
	 * @return boolean
	 */
	public function is_available() {

		return false;

	}

	/**
	 * Get the id of the notification.
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the notification
	 */
	public static function get_item_id( $data ) {

		return $data[ 'like_id' ];

	}

	/**
	 * Get the id of the parent.
	 *
	 * @param array $data The type specific data
	 *
	 * @return integer Always 0.
	 */
	public static function get_item_parent_id( $data ) {

		return 0;

	}

	/**
	 * Find the users who want to receive notifications.
	 *
	 * @param array $data The type specific data
	 * @param array $options Options for finding users for notification
	 * 		ignore_users => array of users and user types that should not receive notifications from this type because they've already been notified
	 * 						e.g.: [2 => [''], 3 => ['', 'email'], ...]
	 *
	 * @return array
	 */
	public function find_users_for_notification( $data, $options = [] ) {

		$users = [];

		$users[ $data[ 'post_author_id' ] ] = $this->notification_manager->get_default_methods();

		return $users;

	}

	/**
	 * Users needed to query before this notification can be displayed.
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query() {

		return [];

	}

	/**
	 * Get the HTML formatted title of this notification.
	 *
	 * @return string
	 */
	public function get_title() {

		/**
		 * Get the username with group colour.
		 */
		$username = $this->user_loader->get_username( $this->get_data( 'user_id' ), 'no_profile' );

		return $this->language->lang( 'UCP_NOTIFIED_POST_LIKED', $username );

	}

	/**
	 * Get the URL to this item.
	 *
	 * @return string URL
	 */
	public function get_url() {

		return append_sid( $this->phpbb_root_path . 'viewtopic.' . $this->php_ext . '?p=' . $this->get_data( 'post_post_id' ) . '#p' . $this->get_data( 'post_post_id' ) );

	}

	/**
	 * Get email template.
	 *
	 * @return string|bool
	 */
	public function get_email_template() {

		return false;

	}

	/**
	 * Get email template variables.
	 *
	 * @return array
	 */
	public function get_email_template_variables() {

		return [];

	}

	/**
	 * Data to save against this notification.
	 *
	 * @param array $data The type specific data
	 * @param array $pre_create_data Data from pre_create_insert_array()
	 */
	public function create_insert_array( $data, $pre_create_data = [] ) {

		$this->set_data( 'user_id', $data[ 'user_id' ] );
		$this->set_data( 'post_author_id', $data[ 'post_author_id' ] );
		$this->set_data( 'post_post_id', $data[ 'post_post_id' ] );

		parent::create_insert_array( $data, $pre_create_data );

	}

}
