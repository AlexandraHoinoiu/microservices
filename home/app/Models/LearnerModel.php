<?php


namespace App\Models;


use Ds\Vector;

class LearnerModel extends Neo4jModel
{
    public string $label = 'Learner';

    public function create($data): void
    {
        $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->neo4jClient->run(
            "CREATE (user:$this->label {
            email:'{$data["email"]}',
            password:'$hashPassword',
            country:'-',
            city:'-',
            firstName:'{$data["first_name"]}',
            lastName:'{$data["last_name"]}',
            profileImg:'{$data["imgPath"]}',
            coverImg:'{$data["coverImg"]}',
            description:'{$data["description"]}'
            })"
        );
    }

    public function getDataByEmail($email): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label {email: '$email'})
            RETURN user, id(user) as id, labels(user) as type"
        );
    }

    public function getLearners(): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label) RETURN ID(user) AS id");
//        $learners = $learner->getLearners();
//        foreach ($learners as $learner) {
//            var_dump($learner->get('id'));
//        }
    }

    public function getLearnerById($id): ?array
    {
        $result = $this->neo4jClient->run("MATCH (user:$this->label) WHERE ID(user) = $id RETURN user");
        if ($result->count() > 0) {
            return $result->first()->get('user');
        }
        return null;
    }

    public function createPost($postId, $userId): Vector
    {
        return $this->neo4jClient->run("MATCH(n:Post) WHERE id(n) = $postId
        MATCH(m:$this->label) WHERE id(m) = $userId
        CREATE (n)-[r:CREATED_BY]->(m)
        RETURN r"
        );
    }
}
