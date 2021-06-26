<?php


namespace App\Clients;


use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AwsClient
{
    private S3Client $client;

    private $bucket;

    public function __construct()
    {
        $awsKey = config('aws.credentials.key');
        $awsSecret = config('aws.credentials.secret');

        $credentials = new Credentials($awsKey, $awsSecret);

        $requestOptions = ['timeout' => 10];
        $options = [
            'version' => config('aws.version'),
            'region' => config('aws.region'),
            'http' => $requestOptions,
            'credentials' => $credentials,
        ];

        $this->client = new S3Client($options);
        $this->bucket = config('aws.bucket');
    }

    public function deleteFolder($path)
    {
        $this->client->deleteMatchingObjects($this->bucket, $path);
    }
}
