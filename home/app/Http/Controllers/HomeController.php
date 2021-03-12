<?php


namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class HomeController extends Controller
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct()
    {
        $user = env('NEO4J_USER');
        $password = env('NEO4J_PASSWORD');
        $this->client = ClientBuilder::create()
            ->addBoltConnection('default', "bolt://$user:$password@neo4j")
            ->build();
    }

    public function checkUser(Request $request): JsonResponse
    {
        $label = $request->get('type');
        $email = $request->get('email');
        $password = $request->get('password');
        $results = $this->client->run("MATCH (user:$label {email: '$email'}) RETURN user.password");
        if ($results->count()) {
            $hashPassword = $results->first()->get('user.password');
            if (password_verify($password, $hashPassword)) {
                return new JsonResponse([
                    'response' => true
                ], 200);
            }
        }
        return new JsonResponse([
            'response' => false,
            'message' => 'Wrong password or email!'
        ], 400);
    }

    public function storeUser(Request $request): JsonResponse
    {
        $label = $request->get('type');
        $email = $request->get('email');
        $password = $request->get('password');
        $results = $this->client->run("MATCH (user:$label {email: '$email'}) RETURN user");
        if ($results->count()) {
            return new JsonResponse([
                'response' => false,
                'message' => 'The user already exist'
            ], 400);
        }
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->client->run("CREATE (user:$label {email:'$email', password:'$hashPassword'})");
        return new JsonResponse([
            'response' => true
        ], 200);
    }
}
