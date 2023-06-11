<?php

namespace App\Services\Posts;


use App\Interfaces\Posts\PostServiceInterface;
use App\Models\Like;
use App\Models\Post;
use App\Repository\Posts\PostRepository;
use Illuminate\Support\Facades\Auth;

class PostService implements PostServiceInterface
{

    public function __construct(private PostRepository $postRepository)
    {
    }

    public function create_post($data)
    {
        return $this->postRepository->create_post($data);
    }
    public function getPosts()
    {
        return  $this->postRepository->with(['user', 'comments.user', 'likes.user', 'shares.user'])->paginate();
    }
    public function getMyPosts()
    {
        return  $this->postRepository->getMyPosts();
    }

    public function create_comment($data)
    {

        return $this->postRepository->create_comment($data);
    }

    public function add_like($data)
    {
        return $this->postRepository->add_like($data);
    }

    public function share_post($data)
    {
        return $this->postRepository->share_post($data);
    }



    public static function increasePostCommentsCount($id)
    {
        $post = Post::find($id);
        $post->comments_count += 1;
        $post->save();
    }

    public static function increasePostLikesCount($id)
    {
        $post = Post::find($id);
        $post->likes_count += 1;
        $post->save();
    }

    public static function decreasePostLikesCount($id)
    {
        $post = Post::find($id);
        $post->likes_count -= 1;
        $post->save();
    }



    public static function increasePostSharesCount($id)
    {
        $post = Post::find($id);
        $post->shares_count += 1;
        $post->save();
    }

    public static function decreasePostSharesCount($id)
    {
        $post = Post::find($id);
        $post->shares_count -= 1;
        $post->save();
    }
}
