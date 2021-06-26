<?php


namespace App\Http\Controllers;


use App\Clients\AwsClient;
use App\Models\Neo4j;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function deleteReport(Request $request): JsonResponse
    {
        try {
            $userType = $request->get('type');
            $idPost = $request->get('idPost');
            $idUser = $request->get('idUser');
            $this->neo4j->neo4jClient->run("MATCH (n:$userType)-[r:REPORT]->(p)
            where id(p) = $idPost and id(n) = $idUser DELETE r");
            return response()->json([
                "status" => 200,
                "success" => true
            ]);
        } catch (Exception $e) {
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
                "MATCH (u:Learner)-[r:REPORT]->(p) return id(u) as idU, labels(u) as type, r.type as reportType, id(p) as idP, id(r) as idR
                 union
                 MATCH (u:School)-[r:REPORT]->(p) return id(u) as idU, labels(u) as type, r.type as reportType, id(p) as idP, id(r) as idR"
            );
            $reports = [];
            if ($result->count()) {
                foreach ($result as $report) {
                    $reports[] = array_merge(
                        ['id' => $report->get('idR')],
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
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getSupervisors(): JsonResponse
    {
        try {
            $result = $this->neo4j->neo4jClient->run("match (n:Supervisor) return n, id(n) as id");
            $supervisors = [];
            if ($result->count()) {
                foreach ($result as $supervisor) {
                    $supervisors[] = array_merge(
                        $supervisor->get('n'),
                        ['id' => $supervisor->get('id')],
                    );
                }
            }
            return response()->json([
                "status" => 200,
                "success" => true,
                "supervisors" => $supervisors
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $userType = $request->get('type');
            $idUser = $request->get('id');
            $this->neo4j->neo4jClient->run("MATCH (p:Post)-[r:CREATED_BY]->(u:$userType)
            WHERE ID(u) = $idUser DETACH DELETE p"
            );
            $this->neo4j->neo4jClient->run("MATCH (n:$userType) where id(n) = $idUser DETACH DELETE n");
            $awsClient = new AwsClient();
            $awsClient->deleteFolder("users/$userType/$idUser");
            return response()->json([
                "status" => 200,
                "success" => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function disableUser(Request $request)
    {
        try {
            date_default_timezone_set('Europe/Bucharest');
            $userType = $request->get('type');
            $idUser = $request->get('id');
            $date = date("Y-m-d H:i:s", strtotime("+1 day"));
            $result = $this->neo4j->neo4jClient->run("MATCH (user:$userType)
            WHERE ID(user) = $idUser
            SET user += {disable: '$date'}
            RETURN user"
            );
            if ($result->count()) {
                return response()->json([
                    "status" => 200,
                    "success" => true
                ]);
            }
            throw new Exception('The user could not be disabled');
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
