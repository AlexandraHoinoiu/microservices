<?php


namespace App\Models;


use Ds\Vector;

class UserModel extends Neo4jModel
{
    protected string $label;

    public function getDataByEmail($email): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label {email: '$email'})
            RETURN user, id(user) as id, labels(user) as type"
        );
    }

    public function createPost($postId, $userId): Vector
    {
        return $this->neo4jClient->run("MATCH(n:Post) WHERE id(n) = $postId
        MATCH(m:$this->label) WHERE id(m) = $userId
        CREATE (n)-[r:CREATED_BY]->(m)
        RETURN r"
        );
    }

    public function reportPost($userId, $postId, $reportType): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label), (post:Post)
        WHERE id(user) = $userId and id(post) = $postId
        CREATE (user)-[r:REPORT {type:'$reportType'}]->(post)
        RETURN r"
        );
    }
}
