<?php


namespace App\Models;


use Ds\Vector;

class UserModel extends Neo4jModel
{
    protected string $label;

    public function getUser($userId): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label)
            WHERE id(user) = $userId
            RETURN user"
        );
    }

    public function changeProfilePhoto($userId, $imgPath): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label)
            WHERE ID(user) = $userId
            SET user.profileImg = '$imgPath'
            RETURN user"
        );
    }

    public function changeCoverPhoto($userId, $imgPath): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label)
            WHERE ID(user) = $userId
            SET user.coverImg = '$imgPath'
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

    public function getFollowersUsers($userId)
    {
        return $this->neo4jClient->run("MATCH (n:$this->label)<-[:FOLLOWS]-(user:Learner)
            WHERE id(n) = $userId
            RETURN user, id(user) as id, labels(user) as type
            UNION
            MATCH (n:$this->label)<-[:FOLLOWS]-(user:School)
            WHERE id(n) = $userId
            RETURN user, id(user) as id, labels(user) as type"
        );
    }
}
