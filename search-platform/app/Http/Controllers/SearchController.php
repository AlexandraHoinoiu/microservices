<?php


namespace App\Http\Controllers;


use Laudis\Neo4j\ClientBuilder;

class SearchController
{
    public function __construct()
    {
        $user = env('NEO4J_USER');
        $password = env('NEO4J_PASSWORD');
        $this->neo4jClient = ClientBuilder::create()
            ->addBoltConnection('default', "bolt://$user:$password@neo4j")
            ->build();
    }

    public function search($word = '')
    {
        try {
            $users = [];
            if (!empty($word)) {
                $result = $this->neo4jClient->run("MATCH (n:Learner)
                where n.firstName =~ '.*$word.*' or n.lastName =~ '.*$word.*'
                return n, id(n) as id, labels(n) as type
                union
                MATCH (n:School)
                where n.name =~ '.*$word.*' return n, id(n) as id, labels(n) as type"
                );
                if ($result->count() > 0) {
                    foreach ($result as $user) {
                        $users[] = array_merge(
                            $user->get('n'),
                            ['id' => $user->get('id')],
                            ['type' => $user->get('type')[0]]
                        );
                    }
                }
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "users" => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 'failed',
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
