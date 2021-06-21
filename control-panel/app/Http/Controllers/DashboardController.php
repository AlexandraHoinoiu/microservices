<?php


namespace App\Http\Controllers;


use App\Models\Neo4j;
use Illuminate\Http\JsonResponse;

class DashboardController
{
    private Neo4j $neo4j;

    public function __construct()
    {
        $this->neo4j = new Neo4j();
    }

    public function getUsers(): JsonResponse
    {
        try {
            $result = $this->neo4j->neo4jClient->run(
                "MATCH (u:Learner) return u, id(u) as id, labels(u) as type
                 union
                 match (u:School) return u, id(u) as id, labels(u) as type"
            );
            $users = [];
            if ($result->count()) {
                foreach ($result as $user) {
                    $users[] = array_merge(
                        $user->get('u'),
                        ['id' => $user->get('id')],
                        ['type' => $user->get('type')[0]]
                    );
                }
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "users" => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getPosts(): JsonResponse
    {
        try {
            $result = $this->neo4j->neo4jClient->run(
                "MATCH (p:Post)-[:CREATED_BY]->(u:Learner)
                return p, id(p) as idP, u, id(u) as idU, labels(u) as type
                UNION
                MATCH (p:Post)-[:CREATED_BY]->(u:School)
                return p, id(p) as idP, u, id(u) as idU, labels(u) as type"
            );
            $posts = [];
            if ($result->count()) {
                foreach ($result as $post) {
                    $posts[] = array_merge(
                        $post->get('p'),
                        ['idPost' => $post->get('idP')],
                        $post->get('u'),
                        ['idUser' => $post->get('idU')],
                        ['type' => $post->get('type')[0]]
                    );
                }
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "posts" => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getReports(): JsonResponse
    {
        try {
            $result = $this->neo4j->neo4jClient->run(
                "MATCH (u:Learner)-[r:REPORT]->(p) return id(u) as idU, labels(u) as type, r.type as reportType, id(p) as idP
                 union
                 MATCH (u:School)-[r:REPORT]->(p) return id(u) as idU, labels(u) as type, r.type as reportType, id(p) as idP"
            );
            $reports = [];
            if ($result->count()) {
                foreach ($result as $report) {
                    $reports[] = array_merge(
                        ['idUser' => $report->get('idU')],
                        ['type' => $report->get('type')[0]],
                        ['reportType' => $report->get('reportType')],
                        ['idPost' => $report->get('idP')]
                    );
                }
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "reports" => $reports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
