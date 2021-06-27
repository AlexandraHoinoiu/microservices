<?php


namespace App\Models;


class PostModel extends Neo4jModel
{
    public string $label = 'Post';

    public function getProfilePosts($type, $userId, $page): array
    {
        $limit = config('profile.limitPosts');
        $skip = $limit * ($page - 1);
        $posts = [];
        $result = $this->neo4jClient->run("MATCH (n:$type)<-[:CREATED_BY]-(p:$this->label)
            where id(n) = $userId
            RETURN DISTINCT p, id(p) as id
            order by p.createdAt DESC
            skip $skip
            limit $limit"
        );
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = array_merge($post->get('p'), ['id' => $post->get('id')]);
            }
        }
        return $posts;
    }
}
