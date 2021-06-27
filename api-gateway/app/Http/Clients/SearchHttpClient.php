<?php


namespace App\Http\Clients;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SearchHttpClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('app_services.url_services.search')]);
    }

    public function searchWord($word =''){
        try{
            $response = $this->client->request(
                'GET',
                sprintf('/search/%s',$word),
                [ 'headers' => ['Content-Type' => 'application/json']]
            );
            return json_decode($response->getBody()->getContents(), true);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return [
                "status" => 'failed',
                "success" => false,
                "message" => 'API request failed'
            ];
        }
    }
}
