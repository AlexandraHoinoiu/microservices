<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ControlPanelHttpClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('app_services.url_services.control-panel')]);
    }

    public function login($email, $password)
    {
        try {
            $jsonBody = json_encode([
                'email' => $email,
                'password' => $password,
            ]);
            $response = $this->client->request(
                'POST',
                '/login',
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

    public function addSupervisor($email, $password, $type)
    {
        try {
            $jsonBody = json_encode([
                'email' => $email,
                'password' => $password,
                'type' => $type,
            ]);
            $response = $this->client->request(
                'POST',
                '/addSupervisor',
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

    public function deleteReport($userType, $userId, $postId)
    {
        try {
            $jsonBody = json_encode([
                'type' => $userType,
                'idPost' => $postId,
                'idUser' => $userId,
            ]);
            $response = $this->client->request(
                'POST',
                '/deleteReport',
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

    public function deleteUser($userType, $userId)
    {
        try {
            $jsonBody = json_encode([
                'type' => $userType,
                'id' => $userId,
            ]);
            $response = $this->client->request(
                'POST',
                '/deleteUser',
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

    public function disableUser($userType, $userId)
    {
        try {
            $jsonBody = json_encode([
                'type' => $userType,
                'id' => $userId,
            ]);
            $response = $this->client->request(
                'POST',
                '/disableUser',
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


    public function getSupervisors()
    {
        try {
            $response = $this->client->request(
                'GET',
                '/supervisors',
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

    public function getReports()
    {
        try {
            $response = $this->client->request(
                'GET',
                '/reports',
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

    public function getUsers()
    {
        try {
            $response = $this->client->request(
                'GET',
                '/users',
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

    public function getPosts()
    {
        try {
            $response = $this->client->request(
                'GET',
                '/posts',
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
