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

}
