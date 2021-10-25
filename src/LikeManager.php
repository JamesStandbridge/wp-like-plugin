<?php 

require_once plugin_dir_path(__FILE__) . "/Repository/LikeRepository.php";

class LikeManager {
    private $userRepository;

    public function __construct()
    {
        $this->likeRepository = new LikeRepository();
    }

    public function changeLikeStatePost(string $user_ip, int $post_id)
    {
        $post = $this->likeRepository->getPostLikedByIp($user_ip, $post_id);

        if(!$post)
            $post = $this->likeRepository->likePost($user_ip, $post_id);
        else
            $post = $this->likeRepository->changeLikeStatePost($post);

        return $post;
    }

    public function getCurrentPostState(string $user_ip, int $post_id)
    {
        return $this->likeRepository->getCurrentPostState($user_ip, $post_id);
    }

    public function countLikes(int $post_id)
    {
        return $this->likeRepository->countLikesForPost($post_id);
    }
}