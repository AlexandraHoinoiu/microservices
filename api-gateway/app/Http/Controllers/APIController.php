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
        $email = $request->get('email','');
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

    }

    public function getUserPosts(Request $request)
    {

    }

    public function createPosts(Request $request)
    {

    }

    public function deletePost(Request $request)
    {

    }

    public function editPost(Request $request)
    {

    }

    public function reportPost(Request $request)
    {

    }

    public function likePost(Request $request)
    {

    }

}
