<?php


namespace App\Models;


use Ds\Vector;

class LearnerModel extends Neo4jModel
{
    public string $label = 'Learner';

    public function getUser($userId): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label)
            WHERE id(user) = $userId
            RETURN user"
        );
    }
    public function getFollowingUsers($userId): Vector
    {
        return $this->neo4jClient->run("MATCH (n:$this->label)-[:FOLLOWS]->(user:Learner)
            WHERE id(n) = $userId
            RETURN user, id(user) as id, labels(user) as type
            UNION
            MATCH (n:$this->label)-[:FOLLOWS]->(user:School)
            WHERE id(n) = $userId
            RETURN user, id(user) as id, labels(user) as type"
        );
    }
}
