<?php


namespace App\Models;


use Ds\Vector;

class SchoolModel extends UserModel
{
    protected string $label = 'School';

    public function editUser($request): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label)
            WHERE ID(user) = $request->userId
            SET user += {
            country: '$request->country',
            city: '$request->city',
            description: '$request->description',
            name: '$request->name'
            }
            RETURN user, id(user) as id, labels(user) as type"
        );
    }
}
