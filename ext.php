<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation;

class ext extends \phpbb\extension\base {

	/**
	 * phpBB 3.3.x please.
	 *
	 * @return boolean
	 */
	public function is_enableable() {

		$config = $this->container->get( 'config' );

		return phpbb_version_compare( $config[ 'version' ], '3.3', '>=' );

	}

	/**
	 * Enable notifications for the extension
	 *
	 * @param	mixed	$old_state	The return value of the previous call
	 *								of this method, or false on the first call
	 * @return	mixed				Returns false after last step, otherwise
	 *								temporary state which is passed as an
	 *								argument to the next step
	 */
	public function enable_step( $old_state ) {

		switch ( $old_state ) {

			case '':

				$notifications = $this->container->get( 'notification_manager' );

				$notifications->enable_notifications( 'danieltj.memberreputation.notification.type.like' );
				$notifications->enable_notifications( 'danieltj.memberreputation.notification.type.dislike' );

				return 'notification';

			break;

			default:

				return parent::enable_step($old_state);

			break;

		}

	}

	/**
	 * Disable notifications for the extension
	 *
	 * @param	mixed	$old_state	The return value of the previous call
	 *								of this method, or false on the first call
	 * @return	mixed				Returns false after last step, otherwise
	 *								temporary state which is passed as an
	 *								argument to the next step
	 */
	public function disable_step( $old_state ) {

		switch ( $old_state ) {

			case '':

				$notifications = $this->container->get( 'notification_manager' );

				$notifications->disable_notifications( 'danieltj.memberreputation.notification.type.like' );
				$notifications->disable_notifications( 'danieltj.memberreputation.notification.type.dislike' );

				return 'notification';

			break;

			default:

				return parent::disable_step( $old_state );

			break;

		}

	}

	/**
	 * Purge notifications for the extension
	 *
	 * @param	mixed	$old_state	The return value of the previous call
	 *								of this method, or false on the first call
	 * @return	mixed				Returns false after last step, otherwise
	 *								temporary state which is passed as an
	 *								argument to the next step
	 */
	public function purge_step( $old_state ) {

		switch ( $old_state ) {

			case '':

				$notifications = $this->container->get( 'notification_manager' );

				$notifications->purge_notifications( 'danieltj.memberreputation.notification.type.like' );
				$notifications->purge_notifications( 'danieltj.memberreputation.notification.type.dislike' );

				return 'notification';

			break;

			default:

				return parent::purge_step( $old_state );

			break;

		}

	}

}
