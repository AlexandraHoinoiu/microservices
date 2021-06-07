<?php


namespace App\Http\Controllers;


use App\Models\LearnerModel;
use App\Models\Neo4jModel;
use App\Models\SchoolModel;
use Exception;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    private Neo4jModel $userModel;

    public function getUserDetails($type, $userId): JsonResponse
    {
        try {
            switch ($type) {
                case 'Learner':
                    $this->userModel = new LearnerModel();
                    break;
                case 'School':
                    $this->userModel = new SchoolModel();
                    break;
                default:
                    throw new Exception('Unrecognized type.');
            }
            $response = $this->userModel->getUser($userId);
            if ($response->count()) {
                return response()->json([
                    "status" => 200,
                    "success" => true,
                    "user" => $response->first()->get('user')
                ]);
            }
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => 'User not found.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getFollowingUsers($type, $userId): JsonResponse
    {
        try {
            switch ($type) {
                case 'Learner':
                    $this->userModel = new LearnerModel();
                    break;
                case 'School':
                    $this->userModel = new SchoolModel();
                    break;
                default:
                    throw new Exception('Unrecognized type.');
            }
            $users = [];
            $response = $this->userModel->getFollowingUsers($userId);
            if ($response->count()) {
                foreach ($response as $node) {
                    $users[] = array_merge(
                    	$node->get('user'), 
                    	['id' => $node->get('id')],
                    	['type' => $node->get('type')[0]]
                    );
                }
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "users" => $users
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
