<?php


namespace App\Http\Controllers;


use App\Http\Clients\HomeHttpClient;
use Illuminate\Http\Request;

class APIController extends Controller
{
    private HomeHttpClient $homeClient;

    public function __construct()
    {
        $this->homeClient = new HomeHttpClient();
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
}
