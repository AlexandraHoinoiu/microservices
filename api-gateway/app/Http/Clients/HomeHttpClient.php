<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HomeHttpClient
{
    private $client;

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
        }
    }
}
