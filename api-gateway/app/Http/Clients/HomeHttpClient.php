<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HomeHttpClient
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('app_services.url_services')]);
    }

    public function signIn($email, $password, $type)
    {
        try {
            $response = $this->client->request('POST', '/signIn',
                http_build_query([
                    'email' => $email,
                    'password' => $password,
                    'type' => $type
                ]));
        } catch (BadResponseException $exception) {
            throw new HttpException(
                $exception->getCode(),
                json_decode($exception->getResponse()->getBody()->getContents())
            );
        }

        return json_decode($response->getBody()->getContents());
    }

    public function signUp($data)
    {
        try {
            $response = $this->client->request('POST', '/signIn', http_build_query($data));
        } catch (BadResponseException $exception) {
            throw new HttpException(
                $exception->getCode(),
                json_decode($exception->getResponse()->getBody()->getContents())
            );
        }

        return json_decode($response->getBody()->getContents());
    }
}
