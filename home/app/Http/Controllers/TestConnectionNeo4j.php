<?php


namespace App\Http\Controllers;


use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Network\Bolt\BoltInjections;

class TestConnectionNeo4j extends Controller
{
    public function index()
    {
        $user = env('NEO4J_USER');
        $password = env('NEO4J_PASSWORD');
        $db = env('NEO4J_DATABASE');
        $injections = BoltInjections::create($db);
        $client = ClientBuilder::create()
            ->addBoltConnection('default', "bolt://$user:$password@neo4j")
            ->build();
        $results = $client->run('MATCH (x) RETURN x LIMIT 100');
        dd($results);
    }
}
