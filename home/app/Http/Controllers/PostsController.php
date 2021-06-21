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
    private AwsClient $awsClient;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->awsClient = new AwsClient();
    }

    public function getFeedPosts(Request $request): JsonResponse
    {
        try {
            $this->getType($request);
            $limit = config('home.limitPosts');
            $userId = $request->get('userId');
            $page = $request->get('page', 1);
            if ($page <= 0) {
                $page = 1;
            }
            $posts = $this->postModel->getFeedPosts($userId, $this->type);
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
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function reportPost(Request $request): JsonResponse
    {
        try {
            $this->getType($request);
            $userId = $request->get('userId');
            $postId = $request->get('postId');
            $reportType = $request->get('reportType');
            $result = $this->userModel->reportPost($userId, $postId, $reportType);
            if ($result->count() > 0) {
                return response()->json([
                    "status" => 200,
                    "success" => true,
                    "message" => 'The post was reported.'
                ]);
            }
            throw new Exception('Something went wrong!');
        } catch (Exception $e) {
            return response()->json([
                "status" => 'failed',
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
            $text = $request->get('text');
            $fileName = $request->get('fileName', '');
            $dataFile = $request->get('dataFile', '');
            $imgUrl = "";
            if (!empty($fileName) && !empty($dataFile)) {
                $new_data = explode(";", $dataFile);
                $data = explode(",", $new_data[1]);
                file_put_contents(storage_path() . '/' . $fileName, base64_decode($data[1]));
                $this->awsClient->uploadFile(storage_path() . '/' . $fileName, 'users/' . $userId . '/posts/' . $fileName);
                $imgUrl = $this->awsClient->getFileUrl('users/' . $userId . '/posts/' . $fileName);
                unlink(storage_path() . '/' . $fileName);
            }
            $postId = $this->postModel->create($text, $imgUrl);
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
        } else if ($this->type == 'School') {
            $this->userModel = new SchoolModel();
        } else {
            throw new Exception('Unrecognized type.');
        }
    }
}
