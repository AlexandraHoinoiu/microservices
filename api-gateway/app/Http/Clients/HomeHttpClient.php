<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class HomeHttpClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('app_services.url_services.home')]);
    }

    public function signIn($email, $password, $type)
    {
        try {
            $jsonBody = json_encode([
                'email' => $email,
                'password' => $password,
                'type' => $type
            ]);
            $response = $this->client->request(
                'POST',
                '/signIn',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function signUp($data)
    {
        try {
            $jsonBody = json_encode($data);
            $response = $this->client->request(
                'POST',
                '/signUp',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function getFeedPosts($type, $userId, $page)
    {
        try {
            $jsonBody = json_encode([
                'userId' => $userId,
                'page' => $page,
                'type' => $type
            ]);
            $response = $this->client->request(
                'POST',
                '/posts',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function reportPost($type, $userId, $postId, $reportType)
    {
        try {
            $jsonBody = json_encode([
                'userId' => $userId,
                'postId' => $postId,
                'reportType' => $reportType,
                'type' => $type
            ]);
            $response = $this->client->request(
                'POST',
                '/reportPost',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function editPost($postId, $text)
    {
        try {
            $jsonBody = json_encode([
                'postId' => $postId,
                'text' => $text,
            ]);
            $response = $this->client->request(
                'POST',
                '/editPost',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function createPost($type, $userId, $text, $fileName, $dataFile)
    {
        try {
            $jsonBody = json_encode([
                'userId' => $userId,
                'fileName' => $fileName,
                'dataFile' => $dataFile,
                'type' => $type,
                'text' => $text,
            ]);
            $response = $this->client->request(
                'POST',
                '/createPost',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function deletePost($postId)
    {
        try {
            $jsonBody = json_encode([
                'postId' => $postId,
            ]);
            $response = $this->client->request(
                'POST',
                '/deletePost',
                ['body' => $jsonBody, 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function getUserPost($postId)
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/post/user/%s', $postId),
                ['headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }

    public function likePost($postId)
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/like/%s', $postId),
                ['headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }
}
