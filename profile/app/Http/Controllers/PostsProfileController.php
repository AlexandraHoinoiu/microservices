<?php


namespace App\Http\Controllers;


use App\Models\PostModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostsProfileController extends Controller
{
    private PostModel $postModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    public function getProfilePosts(Request $request): JsonResponse
    {
        try {
            $limit = config('profile.limitPosts');
            $type = $request->get('type');
            $userId = $request->get('userId');
            $page = $request->get('page', 1);
            if ($page <= 0) {
                $page = 1;
            }
            $posts = $this->postModel->getProfilePosts($type, $userId);
            if (!empty($posts)) {
                usort($posts, function ($a, $b) {
                    return $b['createdAt'] <=> $a['createdAt'];
                });
            }
            $posts = array_chunk($posts, $limit);
            $data = [];
            if (isset($posts[$page - 1])) {
                $data = $posts[$page - 1];
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "data" => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
