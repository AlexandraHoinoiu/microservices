<?php


namespace App\Models;


use Ds\Vector;

class LearnerModel extends UserModel
{
    protected string $label = 'Learner';

    public function editUser($request): Vector
    {
        return $this->neo4jClient->run("MATCH (user:$this->label)
            WHERE ID(user) = $request->userId
            SET user += {country: '$request->country', city: '$request->city',
            description: '$request->description',
            firstName: '$request->firstName',
            lastName: '$request->lastName'
            }
            RETURN user, id(user) as id, labels(user) as type"
        );
    }
}
