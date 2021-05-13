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

    public function uploadFile($body, $key)
    {
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body,
        ]);
    }

    public function getFiles($folder = '')
    {
        try {
            $this->client->registerStreamWrapper();
            $object = $this->client->listObjects([
                'Bucket' => $this->bucket,
                'Prefix' => $folder
            ]);
            return $object['Contents'] ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function downloadFile($key)
    {
        $object = $this->getFileObject($key);
        $fileName = explode('/', $key);
        $fileName = $fileName[count($fileName) - 1];

        header('Content-Description: File Transfer');
        //this assumes content type is set when uploading the file.
        header('Content-Type: ' . $object['ContentType']);
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        //send file to browser for download.
        echo $object['Body'];
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

    public function getUploadData($key)
    {
        $result = $this->getFileObject($key);
        return $result['LastModified'];
    }

    public function getFileContent($key)
    {
        $object = $this->getFileObject($key);
        if (is_object($object['Body'])) {
            return $object['Body']->getContents();
        }
    }

    protected function getFileObject($key): Result
    {
        return $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key
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
