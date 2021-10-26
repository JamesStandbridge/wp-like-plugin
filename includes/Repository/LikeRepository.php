<?php 

class LikeRepository {

    /**
     * Get liked posts by User
     *
     * @return liked[]
     */
    public function getPostLikedByIp(string $user_ip, int $post_id)
    {
        global $wpdb;

        $likedPosts = $wpdb->get_results("
            SELECT *
            FROM {$wpdb->prefix}liked_post AS lp
            WHERE lp.user_ip = '$user_ip' AND lp.post_id = $post_id
        ");

        if(count($likedPosts) === 0)
            return null;
        else
            return $likedPosts[0];
    }

    public function changeLikeStatePost($post)
    {
        global $wpdb;
        $post->is_liked = $post->is_liked == 1 ? 0 : 1;

        $newValue = $post->is_liked;
        $user_ip = $post->user_ip;
        $post_id = $post->post_id;

        $wpdb->query("
            UPDATE {$wpdb->prefix}liked_post SET 
            is_liked = $newValue
            WHERE user_ip = '$user_ip' and post_id = $post_id
        ");

        return  $post;
    }

    public function likePost(string $user_ip, int $post_id)
    {
        global $wpdb;

        $wpdb->query("
            INSERT INTO {$wpdb->prefix}liked_post (user_ip, post_id, is_liked)
            VALUES ('$user_ip', $post_id, true)
        ");

        return $this->getPostLikedByIp($user_ip, $post_id);
    }

    public function getCurrentPostState(string $user_ip, int $post_id)
    {

        $post = $this->getPostLikedByIp($user_ip, $post_id);

        if(!$post)
            return false;
        else
            return $post->is_liked;
    }

    public function countLikesForPost(int $post_id)
    {
        global $wpdb;
       
        $response = $wpdb->query("
            SELECT *
            FROM {$wpdb->prefix}liked_post
            WHERE post_id = $post_id AND is_liked = 1
        ");

        return $response;
    }
}        