<?php

/**
 * @package Member Reputation
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'REPUTATION'					=> 'Reputation',
	'LIKE'							=> 'Like',
	'LIKED'							=> 'Liked',
	'DISLIKE'						=> 'Dislike',
	'DISLIKED'						=> 'Disliked',
	'BUTTON_TITLE_ADD_LIKE'			=> 'Like post',
	'BUTTON_TITLE_REMOVE_LIKE'		=> 'Remove like',
	'BUTTON_HAS_LIKE_POST'			=> 'Liked',
	'BUTTON_LIKE_POST'				=> 'Like',
	'BUTTON_TITLE_ADD_DISLIKE'		=> 'Dislike post',
	'BUTTON_TITLE_REMOVE_DISLIKE'	=> 'Remove dislike',
	'BUTTON_HAS_DISLIKE_POST'		=> 'Disliked',
	'BUTTON_DISLIKE_POST'			=> 'Dislike'
] );
