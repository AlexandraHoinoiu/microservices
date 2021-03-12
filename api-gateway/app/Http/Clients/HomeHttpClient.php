<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;

class HomeHttpClient
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'home.local/']);
    }
}
