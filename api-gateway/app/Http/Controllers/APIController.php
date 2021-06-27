<?php


namespace App\Http\Controllers;


use App\Http\Clients\ControlPanelHttpClient;
use App\Http\Clients\HomeHttpClient;
use App\Http\Clients\ProfileHttpClient;
use App\Http\Clients\SearchHttpClient;
use Illuminate\Http\Request;

class APIController extends Controller
{
    private HomeHttpClient $homeClient;
    private ProfileHttpClient $profileClient;
    private SearchHttpClient $searchClient;
    private ControlPanelHttpClient $controlPanelClient;

    public function __construct()
    {
        $this->homeClient = new HomeHttpClient();
        $this->profileClient = new ProfileHttpClient();
        $this->searchClient = new SearchHttpClient();
        $this->controlPanelClient = new ControlPanelHttpClient();
    }

    public function signIn(Request $request)
    {
        $email = $request->get('email', '');
        $password = $request->get('password', '');
        $type = $request->get('type', '');
        $response = $this->homeClient->signIn($email, $password, $type);
        return response()->json($response);
    }

    public function signUp(Request $request)
    {
        $response = $this->homeClient->signUp($request->all());
        return response()->json($response);
    }

    public function getFeedPosts(Request $request)
    {
        $type = $request->get('type', '');
        $userId = $request->get('userId');
        $page = $request->get('page', 1);
        return response()->json($this->homeClient->getFeedPosts($type, $userId, $page));
    }

    public function getUserPosts(Request $request)
    {

    }

    public function createPosts(Request $request)
    {
        $userId = $request->get('userId');
        $text = $request->get('text');
        $fileName = $request->get('fileName', '');
        $dataFile = $request->get('dataFile', '');
        $type = $request->get('type', '');
        $response = $this->homeClient->createPost($type, $userId, $text, $fileName, $dataFile);
        return response()->json($response);
    }

    public function deletePost(Request $request)
    {
        $postId = $request->get('postId');
        $response = $this->homeClient->deletePost($postId);
        return response()->json($response);
    }

    public function editPost(Request $request)
    {
        $postId = $request->get('postId');
        $text = $request->get('text');
        $response = $this->homeClient->editPost($postId, $text);
        return response()->json($response);
    }

    public function reportPost(Request $request)
    {
        $type = $request->get('type', '');
        $userId = $request->get('userId');
        $postId = $request->get('postId');
        $reportType = $request->get('reportType');
        $response = $this->homeClient->reportPost($type, $userId, $postId, $reportType);
        return response()->json($response);
    }

    public function likePost(Request $request)
    {

    }

    public function getProfilePosts(Request $request)
    {
        $type = $request->get('type');
        $userId = $request->get('userId');
        $page = $request->get('page', 1);
        $response = $this->profileClient->getPosts($type, $userId, $page);
        return response()->json($response);
    }

    public function followUser(Request $request)
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $response = $this->profileClient->follow($followerEmail, $followedEmail, $followedType, $followerType);
        return response()->json($response);
    }

    public function unfollowUser(Request $request)
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $response = $this->profileClient->unfollow($followerEmail, $followedEmail, $followedType, $followerType);
        return response()->json($response);
    }

    public function checkUserFollow(Request $request)
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $response = $this->profileClient->checkUserFollow($followerEmail, $followedEmail, $followedType, $followerType);
        return response()->json($response);
    }

    public function changePhoto(Request $request)
    {
        $userId = $request->get('userId');
        $type = $request->get('type', '');
        $fileName = $request->get('fileName', '');
        $dataFile = $request->get('dataFile', '');
        $photoType = $request->get('photoType', '');
        $response = $this->profileClient->changePhoto($userId, $type, $fileName, $dataFile, $photoType);
        return response()->json($response);
    }

    public function editInfo(Request $request)
    {

    }

    public function getUserDetails()
    {

    }

    public function getFollowingUsers()
    {

    }

    public function getFollowersUsers()
    {

    }

    public function suggestedUsers()
    {

    }
}
