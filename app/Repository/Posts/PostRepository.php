<?php

namespace App\Repository\Posts;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Share;
use App\Repository\BaseRepositoryImplementation;
use App\Services\Posts\PostService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PostRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = Post::query();

        $records->when(isset($filter->orderBy), function ($records) use ($filter) {
            $records->orderBy($filter->getOrderBy(), $filter->getOrder());
        });

        return $records->paginate($filter->per_page);
    }

    public function getMyPosts()
    {
        $records = Post::query()->where('user_id', Auth::id());

        return $records->with(['user', 'comments.user', 'likes.user', 'shares.user'])->paginate();
    }

    public function create_post($data)
    {
        DB::beginTransaction();

        try {

            $post = new Post();

            if (Arr::has($data, 'image')) {
                $file = Arr::get($data, 'image');
                $extention = $file->getClientOriginalExtension();
                $file_name = Str::uuid() . date('Y-m-d') . '.' . $extention;
                $file->move(public_path('images'), $file_name);

                $image_file_path = public_path('images/' . $file_name);
                $image_data = file_get_contents($image_file_path);
                $base64_image = base64_encode($image_data);
                $post->image = $base64_image;
            }
            $post->user_id = Auth::id();
            $post->content = $data['content'];

            $post->save();

            DB::commit();

            return $post->load(['user', 'comments', 'likes', 'shares']);
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function create_comment($data)
    {
        DB::beginTransaction();

        try {

            $comment = new Comment();
            $comment->user_id = Auth::id();
            $comment->post_id = $data['post_id'];
            $comment->content = $data['content'];
            $comment->save();

            DB::commit();

            return $comment->load('user');
        } catch (\Exception $e) {
            DB::rollback();
        }
    }


    public function add_like($data)
    {
        DB::beginTransaction();
        $like = null;
        try {
            $existingLike = Like::where('user_id', Auth::id())
                ->where('post_id', $data['post_id'])
                ->first();

            if ($existingLike) {
                $existingLike->delete();

                PostService::decreasePostLikesCount($data['post_id']);
            } else {
                $like = new Like();
                $like->user_id = Auth::id();
                $like->post_id = $data['post_id'];
                $like->save();

                PostService::increasePostLikesCount($like->post_id);
            }

            DB::commit();

            if ($like != null) {
                return $like->load(['user']);
            } else {
                return $like;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function share_post($data)
    {
        DB::beginTransaction();
        $share = null;
        try {
            $existingShare = Share::where('user_id', Auth::id())
                ->where('post_id', $data['post_id'])
                ->first();

            if ($existingShare) {
                $existingShare->delete();

                PostService::decreasePostSharesCount($data['post_id']);
            } else {
                $share = new Share();
                $share->user_id = Auth::id();
                $share->post_id = $data['post_id'];
                $share->save();

                PostService::increasePostSharesCount($share->post_id);
            }

            DB::commit();

            if ($share != null) {
                return $share->load(['user']);
            } else {
                return $share;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return Post::class;
    }
}
