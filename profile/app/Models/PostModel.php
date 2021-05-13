<?php


namespace App\Models;


class PostModel extends Neo4jModel
{
    public string $label = 'Post';

    public function getProfilePosts($type, $userId): array
    {
        $posts = [];
        $result = $this->neo4jClient->run("MATCH (n:$type)<-[:CREATED_BY]-(p:$this->label)
            where id(n) = $userId
            RETURN p, id(p) as id"
        );
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = array_merge($post->get('p'), ['id' => $post->get('id')]);
            }
        }
        return $posts;
    }
}
