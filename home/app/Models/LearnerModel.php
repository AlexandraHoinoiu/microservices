<?php


namespace App\Models;


class LearnerModel extends UserModel
{
    protected string $label = 'Learner';

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
}
