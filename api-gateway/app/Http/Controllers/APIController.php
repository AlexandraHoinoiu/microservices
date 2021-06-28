<?php


namespace App\Http\Controllers;


use App\Http\Clients\ControlPanelHttpClient;
use App\Http\Clients\HomeHttpClient;
use App\Http\Clients\ProfileHttpClient;
use App\Http\Clients\SearchHttpClient;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Predis\Client as Redis;

class APIController extends Controller
{
    private HomeHttpClient $homeClient;
    private ProfileHttpClient $profileClient;
    private SearchHttpClient $searchClient;
    private ControlPanelHttpClient $controlPanelClient;
    private Redis $redis;

    public function __construct()
    {
        $this->homeClient = new HomeHttpClient();
        $this->profileClient = new ProfileHttpClient();
        $this->searchClient = new SearchHttpClient();
        $this->controlPanelClient = new ControlPanelHttpClient();
        $this->redis = new Redis([
            'host' => 'redis', // docker container name
            'port' => 6379,
        ]);
    }

    public function signIn(Request $request): JsonResponse
    {
        $email = $request->get('email', '');
        $password = $request->get('password', '');
        $type = $request->get('type', '');
        $response = $this->homeClient->signIn($email, $password, $type);
        return response()->json($response);
    }

    public function signUp(Request $request): JsonResponse
    {
        $response = $this->homeClient->signUp($request->all());
        return response()->json($response);
    }

    public function getFeedPosts(Request $request): JsonResponse
    {
        $type = $request->get('type', '');
        $userId = $request->get('userId');
        $page = $request->get('page', 1);
        return response()->json($this->homeClient->getFeedPosts($type, $userId, $page));
    }

    public function getUserPosts($postId): JsonResponse
    {
        $response = $this->homeClient->getUserPost($postId);
        return response()->json($response);
    }

    public function createPosts(Request $request): JsonResponse
    {
        $userId = $request->get('userId');
        $text = $request->get('text');
        $fileName = $request->get('fileName', '');
        $dataFile = $request->get('dataFile', '');
        $type = $request->get('type', '');
        $response = $this->homeClient->createPost($type, $userId, $text, $fileName, $dataFile);
        return response()->json($response);
    }

    public function deletePost(Request $request): JsonResponse
    {
        $postId = $request->get('postId');
        $response = $this->homeClient->deletePost($postId);
        return response()->json($response);
    }

    public function editPost(Request $request): JsonResponse
    {
        $postId = $request->get('postId');
        $text = $request->get('text');
        $response = $this->homeClient->editPost($postId, $text);
        return response()->json($response);
    }

    public function reportPost(Request $request): JsonResponse
    {
        $type = $request->get('type', '');
        $userId = $request->get('userId');
        $postId = $request->get('postId');
        $reportType = $request->get('reportType');
        $response = $this->homeClient->reportPost($type, $userId, $postId, $reportType);
        return response()->json($response);
    }

    public function likePost($postId): JsonResponse
    {
        $response = $this->homeClient->likePost($postId);
        return response()->json($response);
    }

    public function getProfilePosts(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $userId = $request->get('userId');
        $page = $request->get('page', 1);
        $response = $this->profileClient->getPosts($type, $userId, $page);
        return response()->json($response);
    }

    public function followUser(Request $request): JsonResponse
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $response = $this->profileClient->follow($followerEmail, $followedEmail, $followedType, $followerType);
        return response()->json($response);
    }

    public function unfollowUser(Request $request): JsonResponse
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $response = $this->profileClient->unfollow($followerEmail, $followedEmail, $followedType, $followerType);
        return response()->json($response);
    }

    public function checkUserFollow(Request $request): JsonResponse
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $response = $this->profileClient->checkUserFollow($followerEmail, $followedEmail, $followedType, $followerType);
        return response()->json($response);
    }

    public function changePhoto(Request $request): JsonResponse
    {
        $userId = $request->get('userId');
        $type = $request->get('type', '');
        $fileName = $request->get('fileName', '');
        $dataFile = $request->get('dataFile', '');
        $photoType = $request->get('photoType', '');
        $response = $this->profileClient->changePhoto($userId, $type, $fileName, $dataFile, $photoType);
        return response()->json($response);
    }

    public function editInfo(Request $request): JsonResponse
    {
        $response = $this->profileClient->editInfo($request->all());
        return response()->json($response);
    }

    public function getUserDetails($type, $userId): JsonResponse
    {
        $response = $this->profileClient->getUserDetails($type, $userId);
        return response()->json($response);
    }

    public function getFollowingUsers($type, $userId, $limit = ''): JsonResponse
    {
        $response = $this->profileClient->getFollowingUsers($type, $userId, $limit);
        return response()->json($response);
    }

    public function getFollowersUsers($type, $userId, $limit = ''): JsonResponse
    {
        $response = $this->profileClient->getFollowersUsers($type, $userId, $limit);
        return response()->json($response);
    }

    public function suggestedUsers($type, $email, $limit = ''): JsonResponse
    {
        $response = $this->profileClient->suggestedUsers($type, $email, $limit);
        return response()->json($response);
    }

    public function searchWord($word = ''): JsonResponse
    {
        $response = $this->redis->get($word);
        if (!is_null($response)) {
            return response()->json(json_decode($response));
        }
        $response = $this->searchClient->searchWord($word);
        $this->redis->set($word, json_encode($response));
        $this->redis->expire($word, 3600);
        return response()->json($response);
    }

    public function disableUser(Request $request): JsonResponse
    {
        $userType = $request->get('type');
        $idUser = $request->get('id');
        $response = $this->controlPanelClient->disableUser($userType, $idUser);
        return response()->json($response);
    }

    public function deleteUser(Request $request): JsonResponse
    {
        $userType = $request->get('type');
        $idUser = $request->get('id');
        $response = $this->controlPanelClient->deleteUser($userType, $idUser);
        return response()->json($response);
    }

    public function deleteReport(Request $request): JsonResponse
    {
        $userType = $request->get('type');
        $idPost = $request->get('idPost');
        $idUser = $request->get('idUser');
        $response = $this->controlPanelClient->deleteReport($userType, $idUser, $idPost);
        return response()->json($response);
    }

    public function addSupervisor(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $type = $request->get('type');
        $response = $this->controlPanelClient->addSupervisor($email, $password, $type);
        return response()->json($response);
    }

    public function login(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $response = $this->controlPanelClient->login($email, $password);
        return response()->json($response);
    }

    public function getSupervisors(): JsonResponse
    {
        $response = $this->controlPanelClient->getSupervisors();
        return response()->json($response);
    }

    public function getReports(): JsonResponse
    {
        $response = $this->controlPanelClient->getReports();
        return response()->json($response);
    }

    public function getUsers(): JsonResponse
    {
        $response = $this->controlPanelClient->getUsers();
        return response()->json($response);
    }

    public function getPosts(): JsonResponse
    {
        $response = $this->controlPanelClient->getPosts();
        return response()->json($response);
    }
}
