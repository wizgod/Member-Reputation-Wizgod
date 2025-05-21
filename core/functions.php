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

final class functions
{
    protected auth $auth;
    protected database $db;
    protected language $language;
    protected request $request;
    protected user $user;
    protected string $table_prefix;
    protected string $root_path;
    protected string $php_ext;

    public function __construct(
        auth     $auth,
        database $db,
        language $language,
        request  $request,
        user     $user,
        string   $table_prefix,
        string   $root_path,
        string   $php_ext
    )
    {
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
     * @param integer $user_id The post ID of the post to fetch.
     * @return array|boolean The array of post data or false if it doesn't exist.
     * This get is to check the post still exists, whilst i made a sandwich.
     */
    public function get_post(int $post_id): array
    {
        if (empty($post_id)) {
            throw new \phpbb\exception\http_exception(403, 'MY_ERROR');
        }

        $sql = 'SELECT post_id, poster_id FROM ' . POSTS_TABLE . ' WHERE post_id = ' . $post_id;
        $result = $this->db->sql_query($sql);
        $post_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (empty($post_data)) {
            throw new \phpbb\exception\http_exception(404, 'MY_ERROR_POST_DOES_NOT_EXIST');
        }

        return $post_data;
    }

    /**
     * Fetch the number of post likes.
     * @param integer $post_id The post ID to get likes for.
     *
     * @return integer The number of likes for the post.
     */
    public function post_like_count(int $post_id): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM ' . $this->table_prefix . 'reputation WHERE post_post_id = ' . $post_id . ' AND type = 1';
        $result = $this->db->sql_query($sql);
        $count = (int)$this->db->sql_fetchfield('count');
        $this->db->sql_freeresult($result);

        return $count;
    }

    /**
     * Fetch the number of post dislikes.
     * @param integer $post_id The post ID to get dislikes for.
     *
     * @return integer The number of dislikes for the post.
     */
    public function post_dislike_count(int $post_id): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM ' . $this->table_prefix . 'reputation WHERE post_post_id = ' . $post_id . ' AND type = 0';
        $result = $this->db->sql_query($sql);
        $count = (int)$this->db->sql_fetchfield('count');
        $this->db->sql_freeresult($result);

        return $count;
    }

    /**
     * Checks if a user likes a post.
     *
     * @param integer $user_id The user ID of who (maybe) liked the post.
     * @param integer $post_id The post ID that was (maybe) liked.
     *
     * @return boolean
     */
    public function has_liked_post(int $user_id, int $post_id): bool
    {
        $sql = 'SELECT post_post_id FROM ' . $this->table_prefix . "reputation WHERE user_id = {$user_id} AND post_post_id = {$post_id} AND type = 1";
        $result = $this->db->sql_query($sql);
        $exists = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        return !empty($exists);
    }

    /**
     * Checks if a user dislikes a post.
     *
     * @param integer $user_id The user ID of who (maybe) disliked the post.
     * @param integer $post_id The post ID that was (maybe) disliked.
     *
     * @return boolean
     */
    public function has_disliked_post(int $user_id, int $post_id): bool
    {
        $sql = 'SELECT post_post_id FROM ' . $this->table_prefix . "reputation WHERE user_id = {$user_id} AND post_post_id = {$post_id} AND type = 0";
        $result = $this->db->sql_query($sql);
        $exists = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        return !empty($exists);
    }

    /**
     * Like a post.
     *
     * @param integer $user_id The user ID of who likes the post.
     * @param integer $post_author_id The user ID of the post author.
     * @param integer $post_id The post ID that was liked.
     *
     * @return integer The next id.
     */
    public function like_post(int $user_id, int $post_author_id, int $post_id): int
    {
        $sql_insert = [
            'user_id' => $user_id,
            'post_author_id' => $post_author_id,
            'post_post_id' => $post_id,
            'type' => 1,
            'created_at' => time(),
        ];

        $sql = 'INSERT INTO ' . $this->table_prefix . 'reputation ' . $this->db->sql_build_array('INSERT', $sql_insert);
        $this->db->sql_query($sql);
        $this->db->sql_transaction('commit');

        return $this->db->sql_nextid();
    }

    /**
     * Dislike a post.
     *
     * @param integer $user_id The user ID of who dislikes the post.
     * @param integer $post_author_id The user ID of the post author.
     * @param integer $post_id The post ID that was disliked.
     *
     * @return integer The next id.
     */
    public function dislike_post(int $user_id, int $post_author_id, int $post_id): int
    {
        $sql_insert = [
            'user_id' => $user_id,
            'post_author_id' => $post_author_id,
            'post_post_id' => $post_id,
            'type' => 0,
            'created_at' => time(),
        ];

        $sql = 'INSERT INTO ' . $this->table_prefix . 'reputation ' . $this->db->sql_build_array('INSERT', $sql_insert);
        $this->db->sql_query($sql);
        $this->db->sql_transaction('commit');

        return $this->db->sql_nextid();
    }

    /**
     * Unlike a post.
     *
     * @param integer $user_id The user ID of who is removing their like.
     * @param integer $post_id The post ID that is no longer liked.
     */
    public function remove_like(int $user_id, int $post_id): void
    {
        $sql = 'DELETE FROM ' . $this->table_prefix . 'reputation WHERE user_id = ' . $user_id . ' AND post_post_id = ' . $post_id . ' AND type = 1';
        $this->db->sql_query($sql);
        $this->db->sql_transaction('commit');
    }

    /**
     * Remove dislike.
     *
     * @param integer $user_id The user ID of who is removing their dislike.
     * @param integer $post_id The post ID that is no longer disliked.
     */
    public function remove_dislike(int $user_id, int $post_id): void
    {
        $sql = 'DELETE FROM ' . $this->table_prefix . 'reputation WHERE user_id = ' . $user_id . ' AND post_post_id = ' . $post_id . ' AND type = 0';
        $this->db->sql_query($sql);
        $this->db->sql_transaction('commit');
    }

    /**
     * Return a user's reputation score.
     *
     * @param integer $user_id The user ID who's rep score we want.
     *
     * @return integer
     */
    public function user_rep_score(int $user_id): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM ' . $this->table_prefix . 'reputation WHERE post_author_id = ' . $user_id . ' AND type = 1';
        $result = $this->db->sql_query($sql);
        $liked = (int)($this->db->sql_fetchfield('total') ?: 0);
        $this->db->sql_freeresult($result);

        $sql = 'SELECT COUNT(*) AS total FROM ' . $this->table_prefix . 'reputation WHERE post_author_id = ' . $user_id . ' AND type = 0';
        $result = $this->db->sql_query($sql);
        $disliked = (int)($this->db->sql_fetchfield('total') ?: 0);
        $this->db->sql_freeresult($result);

        $total = $liked - $disliked;
        if ($total < 0) {
            $total = 0;
        }

        return $total;
    }
}
