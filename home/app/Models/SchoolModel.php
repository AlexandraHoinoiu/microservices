<?php


namespace App\Models;


class SchoolModel extends UserModel
{
    protected string $label = 'School';

    public function create($data): void
    {
        $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->neo4jClient->run(
            "CREATE (user:$this->label {
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
}
