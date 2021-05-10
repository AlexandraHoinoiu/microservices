<?php


namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class FollowController
{
    private ClientInterface $neo4jClient;

    public function __construct()
    {
        $user = env('NEO4J_USER');
        $password = env('NEO4J_PASSWORD');
        $this->neo4jClient = ClientBuilder::create()
            ->addBoltConnection('default', "bolt://$user:$password@neo4j")
            ->build();
    }

    public function followUser(Request $request): JsonResponse
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $result = $this->neo4jClient->run("MATCH (follower:$followerType {email: '$followerEmail'}),
        (followed:$followedType {email: '$followedEmail'})
        RETURN EXISTS((follower)-[:FOLLOWS]-(followed)) as result");
        if ($result->count() <= 0) {
            return response()->json([
                "status" => 'failed',
                "success" => false,
                "message" => 'The users could not be found.'
            ]);
        }
        if (!$result->first()->get('result')) {
            $this->neo4jClient->run("MATCH (follower:$followerType {email: '$followerEmail'}),
                        (followed:$followedType {email: '$followedEmail'})
                        CREATE (follower)-[r:FOLLOWS]->(followed)
                        RETURN r"
            );
            $message = 'OK';
        } else {
            $message = 'The user is already followed.';
        }
        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => $message
        ]);
    }

    public function unfollowUser(Request $request): JsonResponse
    {
        $followerEmail = $request->get('followerEmail');
        $followedEmail = $request->get('followedEmail');
        $followerType = $request->get('followerType');
        $followedType = $request->get('followedType');
        $result = $this->neo4jClient->run("MATCH (follower:$followerType {email: '$followerEmail'}),
        (followed:$followedType {email: '$followedEmail'})
        RETURN EXISTS((follower)-[:FOLLOWS]-(followed)) as result");
        if ($result->count() <= 0) {
            return response()->json([
                "status" => 'failed',
                "success" => false,
                "message" => 'The users could not be found.'
            ]);
        }
        if ($result->first()->get('result')) {
            $this->neo4jClient->run("MATCH (follower:$followerType {email: '$followerEmail'})-
            [r:FOLLOWS]->(followed:$followedType {email: '$followedEmail'}) DELETE r");
            $message = 'OK';
        } else {
            $message = 'This user is not in the list with followed users.';
        }
        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => $message
        ]);
    }

}
