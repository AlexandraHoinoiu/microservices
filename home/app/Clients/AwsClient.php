<?php


namespace App\Clients;


use Aws\Credentials\Credentials;
use Aws\Result;
use Aws\S3\S3Client;
use Exception;

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

    public function uploadFile($path, $key): Result
    {
        return $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SourceFile' => $path,
            'ACL' => 'public-read'
        ]);
    }

    public function fileExist($filename): bool
    {
        return $this->client->doesObjectExist($this->bucket, $filename);
    }

    public function deleteFile($filename)
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $filename
        ]);
    }

    public function getFileUrl($key): ?string
    {
        if ($this->fileExist($key)) {
            return $this->client->getObjectUrl($this->bucket, $key);
        }
        return null;
    }
}
