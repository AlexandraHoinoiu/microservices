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
        return $this->neo4jClient->run("MATCH (user:$this->label)-[:FOLLOWS]->(:Learner)
            WHERE id(user) = $userId
            RETURN user, id(user) as id
            UNION
            MATCH (user:$this->label)-[:FOLLOWS]->(:School)
            WHERE id(user) = $userId
            RETURN user, id(user) as id"
        );
    }
}
