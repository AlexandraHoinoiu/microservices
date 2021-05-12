<?php


namespace App\Http\Controllers;


use App\Clients\AwsClient;
use App\Models\LearnerModel;
use App\Models\Neo4jModel;
use App\Models\PostModel;
use App\Models\SchoolModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostsController
{
    private PostModel $postModel;
    private Neo4jModel $userModel;
    private string $type;

    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    public function getFeedPosts(Request $request): JsonResponse
    {
//        $awsClient = new AwsClient();
//        var_dump($awsClient->getFileUrl('3.jpeg'));exit;
        try {
            $this->getType($request);
            $limit = config('posts.limitPosts');
            $userId = $request->get('userId');
            $page = $request->get('page', 1);
            if ($page <= 0) {
                $page = 1;
            }
            $posts = $this->postModel->getFeedPosts($userId, $this->type);
            if (!empty($posts)) {
                usort($posts, function ($a, $b) {
                    return $a['createdAt'] <=> $b['createdAt'];
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
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function createPost(Request $request): JsonResponse
    {
        try {
            $this->getType($request);
            $userId = $request->get('userId');
            $postId = $this->postModel->create($request->all());
            if (!is_null($postId)) {
                $result = $this->userModel->createPost($postId, $userId);
                if ($result->count() > 0) {
                    return response()->json([
                        "status" => 200,
                        "success" => true,
                        "message" => 'The post has been created.'
                    ]);
                }
                $this->postModel->delete($postId);
            }
            return response()->json([
                "status" => 'failed',
                "success" => false,
                "message" => 'The post could not be created.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function deletePost(Request $request): JsonResponse
    {
        $postId = $request->get('postId');
        try {
            $this->postModel->delete($postId);
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => 'The post was deleted.'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' file ' . $e->getFile() . ' line ' . $e->getLine());
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'The post could not be deleted.'
        ]);
    }

    public function editPost(Request $request): JsonResponse
    {
        $postId = $request->get('postId');
        $newText = $request->get('text');
        $response = $this->postModel->editText($postId, $newText);
        if ($response->count() > 0 && $response->first()->get('post')) {
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => 'The post was successfully edited.'
            ]);
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'The post could not be edited.'
        ]);
    }

    public function likePost($postId): JsonResponse
    {
        $response = $this->postModel->modifyLikes($postId, 1);
        if ($response->count() > 0 && $response->first()->get('post')) {
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => 'OK'
            ]);
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'Error'
        ]);
    }

    public function dislikePost($postId): JsonResponse
    {
        $response = $this->postModel->modifyDislikes($postId, 1);
        if ($response->count() > 0 && $response->first()->get('post')) {
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => 'OK'
            ]);
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'Error'
        ]);
    }

    public function removeLikePost($postId): JsonResponse
    {
        $response = $this->postModel->modifyLikes($postId, -1);
        if ($response->count() > 0 && $response->first()->get('post')) {
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => 'OK'
            ]);
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'Error'
        ]);
    }

    public function removeDislikePost($postId): JsonResponse
    {
        $response = $this->postModel->modifyDislikes($postId, -1);
        if ($response->count() > 0 && $response->first()->get('post')) {
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => 'OK'
            ]);
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'Error'
        ]);
    }

    public function getUserPost($postId): JsonResponse
    {
        $response = $this->postModel->getUser($postId);
        if ($response->count() > 0) {
            return response()->json([
                "status" => 200,
                "success" => true,
                "user" => array_merge(
                    $response->first()->get('user'),
                    ['type' => $response->first()->get('type')[0]],
                    ['id' => $response->first()->get('id')]
                )
            ]);
        }
        return response()->json([
            "status" => 'failed',
            "success" => false,
            "message" => 'User not found'
        ]);
    }

    /**
     * @throws Exception
     */
    private function getType(Request $request): void
    {
        $this->type = $request->get('type', '');
        if ($this->type == 'Learner') {
            $this->userModel = new LearnerModel();
        } else if ($this->type == SchoolModel::$LABEL) {
            $this->userModel = new SchoolModel();
        } else {
            throw new Exception('Unrecognized type.');
        }
    }
}
