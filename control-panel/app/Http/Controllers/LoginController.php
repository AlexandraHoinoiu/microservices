<?php


namespace App\Http\Controllers;

use App\Models\Neo4j;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    private Neo4j $neo4j;

    public function __construct()
    {
        $this->neo4j = new Neo4j();
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(),
                [
                    "email" => "required|email",
                    "password" => "required"
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "status" => "failed",
                    "message" => $validator->errors()
                ]);
            }
            $email = $request->get('email');
            $password = $request->get('password');

            $results = $this->neo4j->neo4jClient->run("MATCH (user:Supervisor {email: '$email'}) RETURN user");
            if ($results->count()) {
                $user = $results->first()->get('user');
                if (password_verify($password, $user['password'])) {
                    return response()->json([
                        "status" => 200,
                        "success" => true,
                        "message" => "You have logged in successfully",
                        "user" => $user
                    ]);
                } else {
                    return response()->json([
                        "status" => "failed",
                        "success" => false,
                        "message" => "Unable to login. Incorrect password."
                    ]);
                }
            }
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => "Unable to login. Email doesn't exist."
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' File: ' . $e->getFile() . ' line:' . $e->getLine());
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => "Unable to login."
            ]);
        }
    }

    public function addSupervisor(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(),
                [
                    "email" => "required|email",
                    "password" => "required",
                    "type" => "required"
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "status" => "failed",
                    "message" => $validator->errors()
                ]);
            }
            $email = $request->get('email');
            $password = $request->get('password');
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $type = $request->get('type');

            $results = $this->neo4j->neo4jClient->run("MATCH (user:Supervisor {email: '$email'}) RETURN user");
            if (!$results->count()) {
                $this->neo4j->neo4jClient->run(
                    "CREATE (user:Supervisor {
                email: '$email',
                password: '$hashPassword',
                type: '$type'
                })"
                );
                return response()->json([
                    "status" => 200,
                    "success" => true,
                    "message" => "Registration completed successfully",
                ]);
            }
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => "Email already registered!"
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' File: ' . $e->getFile() . ' line:' . $e->getLine());
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
