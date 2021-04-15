<?php


namespace App\Models;


use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

abstract class Neo4jModel
{
    protected ClientInterface $neo4jClient;

    public function __construct()
    {
        $user = env('NEO4J_USER');
        $password = env('NEO4J_PASSWORD');
        $this->neo4jClient = ClientBuilder::create()
            ->addBoltConnection('default', "bolt://$user:$password@neo4j")
            ->build();
    }
}
