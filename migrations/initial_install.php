<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\memberreputation\migrations;

class initial_install extends \phpbb\db\migration\migration {

	/**
	 * Already installed?
	 */
	public function effectively_installed() {

		return $this->db_tools->sql_table_exists( $this->table_prefix . 'likes' );

	}

	/**
	 * Needs v314rc1.
	 */
	static public function depends_on() {

		return [ '\phpbb\db\migration\data\v31x\v314rc1' ];

	}

	/**
	 * Save ext version.
	 */
	public function update_data() {

		return [
			[ 'permission.add', [ 'u_can_like' ] ],
			[ 'permission.add', [ 'u_can_dislike' ] ]
		];

	}

	/**
	 * Add reputation table.
	 */
	public function update_schema() {

		return [
			'add_tables' => [
				$this->table_prefix . 'reputation' => [
					'COLUMNS' 				=> [
						'user_id'			=> [ 'INT:9', 0 ],
						'post_author_id'	=> [ 'INT:9', 0 ],
						'post_post_id'		=> [ 'INT:9', 0 ],
						'type'				=> [ 'INT:9', 0 ],
						'created_at'		=> [ 'VCHAR:48', '' ]
					],
					'PRIMARY_KEY'	=> 'user_id, post_author_id, post_post_id'
				]
			]
		];

	}

	/**
	 * Delete reputation table.
	 */
	public function revert_schema() {

		return [
			'drop_tables' => [
				$this->table_prefix . 'reputation'
			]
		];

	}

}
