<?php


namespace App\Models;


use Ds\Vector;

class PostModel extends Neo4jModel
{
    public string $label = 'Post';

    public function create($data): ?int
    {
        $createdAt = gmdate('Y-m-d H:i:s');
        $response = $this->neo4jClient->run(
            "CREATE (post:$this->label {
            createdAt:'$createdAt',
            updatedAt:'$createdAt',
            text:'{$data['text']}',
            imgPath:'{$data['imgPath']}',
            likes:0,
            dislikes:0
            }) return ID(post) as id"
        );
        if ($response->count() > 0) {
            return $response->first()->get('id');
        }
        return null;
    }

    public function delete($id): Vector
    {
        return $this->neo4jClient->run("MATCH (post:$this->label) WHERE ID(post) = $id DETACH DELETE post");
    }

    public function getFeedPosts($userId, $type): array
    {
        $posts = [];
        $result = $this->neo4jClient->run("MATCH (n:$type)-[:FOLLOWS]->(:Learner)--(p:$this->label)
            where id(n) = $userId RETURN p");
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = $post->get('p');
            }
        }
        $result = $this->neo4jClient->run("MATCH (n:$type)-[:FOLLOWS]->(:School)--(p:$this->label)
            where id(n) = $userId
            RETURN p"
        );
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = $post->get('p');
            }
        }
        $result = $this->neo4jClient->run("MATCH (n:$type)<-[:CREATED_BY]-(p:$this->label)
            where id(n) = $userId
            RETURN p"
        );
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = $post->get('p');
            }
        }
        return $posts;
    }

    public function editText($postId, $newText): Vector
    {
        return $this->neo4jClient->run("MATCH (post:$this->label)
            WHERE ID(post) = $postId
            SET post.text = '$newText'
            RETURN post"
        );
    }
}
