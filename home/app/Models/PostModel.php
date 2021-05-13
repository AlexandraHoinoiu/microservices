<?php


namespace App\Models;


use Ds\Vector;

class PostModel extends Neo4jModel
{
    public string $label = 'Post';

    public function create($text, $imgPath): ?int
    {
        $createdAt = gmdate('Y-m-d H:i:s');
        $response = $this->neo4jClient->run(
            "CREATE (post:$this->label {
            createdAt:'$createdAt',
            updatedAt:'$createdAt',
            text:'$text',
            imgPath:'$imgPath',
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
            where id(n) = $userId RETURN p, id(p) as id");
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = array_merge($post->get('p'), ['id' => $post->get('id')]);
            }
        }
        $result = $this->neo4jClient->run("MATCH (n:$type)-[:FOLLOWS]->(:School)--(p:$this->label)
            where id(n) = $userId
            RETURN p , id(p) as id"
        );
        if ($result->count()) {
            foreach ($result as $post) {
                $posts[] = array_merge($post->get('p'), ['id' => $post->get('id')]);
            }
        }
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

    public function editText($postId, $newText): Vector
    {
        return $this->neo4jClient->run("MATCH (post:$this->label)
            WHERE ID(post) = $postId
            SET post.text = '$newText'
            RETURN post"
        );
    }

    public function modifyLikes($postId, $number): Vector
    {
        return $this->neo4jClient->run("MATCH (post:$this->label)
            WHERE ID(post) = $postId
            SET post.likes = post.likes + $number
            RETURN post"
        );
    }

    public function modifyDislikes($postId, $number): Vector
    {
        return $this->neo4jClient->run("MATCH (post:$this->label)
            WHERE ID(post) = $postId
            SET post.dislikes = post.dislikes + $number
            RETURN post"
        );
    }

    public function getUser($postId): Vector
    {
        return $this->neo4jClient->run("MATCH (user:Learner)<-[:CREATED_BY]-(p:$this->label)
            WHERE id(p) = $postId RETURN user, labels(user) as type, id(user) as id
            UNION
            MATCH (user:School)<-[:CREATED_BY]-(p:$this->label)
            WHERE id(p) = $postId RETURN user, labels(user) as type, id(user) as id"
        );
    }
}
