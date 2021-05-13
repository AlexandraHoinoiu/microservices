<?php


namespace App\Models;


use Ds\Vector;

class SchoolModel extends Neo4jModel
{
    public static string $LABEL = 'School';

    public function create($data): void
    {
        $label = self::$LABEL;
        $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->neo4jClient->run(
            "CREATE (user:$label {
            email:'{$data["email"]}',
            password:'$hashPassword',
            country:'-',
            city:'-',
            name:'{$data["name"]}',
            profileImg:'{$data["imgPath"]}',
            coverImg:'{$data["coverImg"]}',
            description:'{$data["description"]}'
            })"
        );
    }

    public function getDataByEmail($email): Vector
    {
        $label = self::$LABEL;
        return $this->neo4jClient->run("MATCH (user:$label {email: '$email'})
            RETURN user, id(user) as id, labels(user) as type"
        );
    }

    public function createPost($postId, $userId): Vector
    {
        $label = self::$LABEL;
        return $this->neo4jClient->run("MATCH(n:Post) WHERE id(n) = $postId
        MATCH(m:$label) WHERE id(m) = $userId
        CREATE (n)-[r:CREATED_BY]->(m)
        RETURN r"
        );
    }
}
