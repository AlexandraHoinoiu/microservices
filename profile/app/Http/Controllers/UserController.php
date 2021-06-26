<?php


namespace App\Http\Controllers;


use App\Clients\AwsClient;
use App\Models\LearnerModel;
use App\Models\Neo4jModel;
use App\Models\SchoolModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private Neo4jModel $userModel;
    private AwsClient $awsClient;
    const COVER_PHOTO = 'cover';
    const PROFILE_PHOTO = 'profile';

    public function __construct()
    {
        $this->awsClient = new AwsClient();
    }

    public function getUserDetails($type, $userId): JsonResponse
    {
        try {
            $this->getUserType($type);
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

    public function getFollowingUsers($type, $userId, $limit = ''): JsonResponse
    {
        try {
            $this->getUserType($type);
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
                if (!empty($limit) && $limit < count($users)){
                    $users = array_slice($users, 0, $limit);
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

    public function getFollowersUsers($type, $userId, $limit = ''): JsonResponse
    {
        try {
            $this->getUserType($type);
            $users = [];
            $response = $this->userModel->getFollowersUsers($userId);
            if ($response->count()) {
                foreach ($response as $node) {
                    $users[] = array_merge(
                        $node->get('user'),
                        ['id' => $node->get('id')],
                        ['type' => $node->get('type')[0]]
                    );
                }
                if (!empty($limit) && $limit < count($users)){
                    $users = array_slice($users, 0, $limit);
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

    public function changePhoto(Request $request): JsonResponse
    {
        try {
            $userId = $request->get('userId');
            $type = $request->get('type', '');
            $fileName = $request->get('fileName', '');
            $dataFile = $request->get('dataFile', '');
            $photoType = $request->get('photoType', '');
            $this->getUserType($type);
            if (!empty($fileName) && !empty($dataFile) && in_array($photoType, [self::COVER_PHOTO, self::PROFILE_PHOTO])) {
                $new_data = explode(";", $dataFile);
                $data = explode(",", $new_data[1]);
                file_put_contents(storage_path() . '/' . $fileName, base64_decode($data[1]));
                $awsKey = "users/$type/$userId/profile/" . time() . "-$fileName";
                $this->awsClient->uploadFile(
                    storage_path() . '/' . $fileName,
                    $awsKey
                );
                $imgUrl = $this->awsClient->getFileUrl($awsKey);
                unlink(storage_path() . '/' . $fileName);
                if ($photoType == self::COVER_PHOTO) {
                    $response = $this->userModel->changeCoverPhoto($userId, $imgUrl, $this->awsClient);
                } else {
                    $response = $this->userModel->changeProfilePhoto($userId, $imgUrl, $this->awsClient);
                }
                if ($response->count()) {
                    return response()->json([
                        "status" => 200,
                        "success" => true,
                        "link" => $imgUrl
                    ]);
                }
            }
            throw new Exception('The update could not be done!');
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function editUser(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type', '');
            $this->getUserType($type);
            $response = $this->userModel->editUser($request);
            if ($response->count()) {
                $user = array_merge(
                    $response->first()->get('user'),
                    ['type' => $response->first()->get('type')[0]],
                    ['id' => $response->first()->get('id')]
                );
                return response()->json([
                    "status" => 200,
                    "success" => true,
                    "user" => $user
                ]);
            }
            throw new Exception('The update could not be done!');
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    private function getUserType($type): void
    {
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
    }
}
