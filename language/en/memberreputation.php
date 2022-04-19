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
	'BUTTON_DISLIKE_POST'			=> 'Dislike',

	'CANNOT_PASS_BAD_CSRF_TOKEN'	=> 'You provided an incorrect or invalid CSRF token.',
	'CANNOT_LIKE_NO_EXIST_POST'		=> 'You cannot like a post that doesn\'t exist.',
	'CANNOT_DISLIKE_NO_EXIST_POST'	=> 'You cannot dislike a post that doesn\'t exist.',
	'CANNOT_LIKE_GUEST_POSTS'		=> 'You cannot like a guests post.',
	'CANNOT_LIKE_ANY_POSTS'			=> 'You cannot like anyones post.',
	'CANNOT_LIKE_OWN_POSTS'			=> 'You cannot like your own posts.',
	'CANNOT_DISLIKE_GUEST_POSTS'	=> 'You cannot dislike a guests post.',
	'CANNOT_DISLIKE_ANY_POSTS'		=> 'You cannot dislike anyones post.',
	'CANNOT_DISLIKE_OWN_POSTS'		=> 'You cannot dislike your own posts.',

	'INFO_POST_WAS_LIKED'			=> 'You have liked this post.',
	'INFO_POST_WAS_UNLIKED'			=> 'You have removed your like from this post.',
	'INFO_POST_WAS_DISLIKED'		=> 'You have disliked this post.',
	'INFO_POST_WAS_UNDISLIKED'		=> 'You have removed your dislike from this post.',

	'POST_LIKED_BY_ONE_DISLIKED_BY_NONE'		=> '%s person likes this post.',
	'POST_LIKED_BY_ONE_DISLIKED_BY_ONE'			=> '%s person likes and %s person dislikes this post.',
	'POST_LIKED_BY_ONE_DISLIKED_BY_MANY'		=> '%s person likes and %s people dislike this post.',
	'POST_LIKED_BY_MANY_DISLIKED_BY_NONE'		=> '%s people like this post.',
	'POST_LIKED_BY_MANY_DISLIKED_BY_ONE'		=> '%s people like and %s person dislikes this post.',
	'POST_LIKED_BY_MANY_DISLIKED_BY_MANY'		=> '%s people like and %s people dislike this post.',
	'POST_LIKED_BY_NONE_DISLIKED_BY_ONE'		=> '%s person dislikes this post.',
	'POST_LIKED_BY_NONE_DISLIKED_BY_MANY'		=> '%s people dislike this post.'
] );
