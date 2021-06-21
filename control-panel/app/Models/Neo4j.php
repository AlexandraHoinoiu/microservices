<?php


namespace App\Models;


use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class Neo4j
{
    public ClientInterface $neo4jClient;

    public function __construct()
    {
        $user = env('NEO4J_USER');
        $password = env('NEO4J_PASSWORD');
        $this->neo4jClient = ClientBuilder::create()
            ->addBoltConnection('default', "bolt://$user:$password@neo4j")
            ->build();
    }
}
