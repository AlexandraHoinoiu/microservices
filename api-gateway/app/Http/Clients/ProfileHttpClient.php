<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ProfileHttpClient
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('app_services.url_services.profile')]);
    }

    public function getPosts($type, $userId, $page)
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

    public function follow($followerEmail, $followedEmail, $followedType, $followerType)
    {
        try {
            $jsonBody = json_encode([
                'followedEmail' => $followedEmail,
                'followedType' => $followedType,
                'followerEmail' => $followerEmail,
                'followerType' => $followerType,
            ]);
            $response = $this->client->request(
                'POST',
                '/follow',
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


    public function unfollow($followerEmail, $followedEmail, $followedType, $followerType)
    {
        try {
            $jsonBody = json_encode([
                'followedEmail' => $followedEmail,
                'followedType' => $followedType,
                'followerEmail' => $followerEmail,
                'followerType' => $followerType,
            ]);
            $response = $this->client->request(
                'POST',
                '/unfollow',
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

    public function checkUserFollow($followerEmail, $followedEmail, $followedType, $followerType)
    {
        try {
            $jsonBody = json_encode([
                'followedEmail' => $followedEmail,
                'followedType' => $followedType,
                'followerEmail' => $followerEmail,
                'followerType' => $followerType,
            ]);
            $response = $this->client->request(
                'POST',
                '/checkUserFollow',
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

    public function changePhoto($userId, $type, $fileName, $dataFile, $photoType)
    {
        try {
            $jsonBody = json_encode([
                'userId' => $userId,
                'type' => $type,
                'fileName' => $fileName,
                'dataFile' => $dataFile,
                'photoType' => $photoType
            ]);
            $response = $this->client->request(
                'POST',
                '/user/changePhoto',
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
}
