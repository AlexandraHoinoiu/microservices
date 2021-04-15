<?php


namespace App\Models;


use Ds\Vector;

class LearnerModel extends Neo4jModel
{
    public static string $LABEL = 'Learner';

    public function create($data): void
    {
        $label = self::$LABEL;
        $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->neo4jClient->run(
            "CREATE (user:$label {
            email:'{$data["email"]}',
            password:'$hashPassword',
            firstName:'{$data["first_name"]}',
            lastName:'{$data["last_name"]}'
            })"
        );
    }

    public function getDataByEmail($email): Vector
    {
        $label = self::$LABEL;
        return $this->neo4jClient->run("MATCH (user:$label {email: '$email'}) RETURN user");
    }
}
