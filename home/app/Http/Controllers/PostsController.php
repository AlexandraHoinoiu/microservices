<?php


namespace App\Http\Controllers;


use App\Clients\AwsClient;
use App\Models\LearnerModel;
use App\Models\Neo4jModel;
use App\Models\PostModel;
use App\Models\SchoolModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostsController
{
    private PostModel $postModel;
    private Neo4jModel $userModel;
    private string $type;
    private static array $routesWithoutType = [
        'editPost',
        'deletePost'
    ];

    public function __construct(Request $request)
    {
        $this->postModel = new PostModel();
        $this->type = $request->get('type', '');
        if ($this->type == 'Learner') {
            $this->userModel = new LearnerModel();
        } else if ($this->type == SchoolModel::$LABEL) {
            $this->userModel = new SchoolModel();
        } else if (!in_array($request->path(), self::$routesWithoutType)) {
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "failed",
                "message" => "validation_error",
                "errors" => 'Unrecognized type.'
            ]);
            exit;
        }
    }

    public function getFeedPosts(Request $request): JsonResponse
    {
//        $awsClient = new AwsClient();
//        var_dump($awsClient->getFileUrl('3.jpeg'));exit;
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
    }

    public function createPost(Request $request): JsonResponse
    {
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
        } catch (\Exception $e) {
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
}
