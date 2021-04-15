<?php


namespace App\Http\Controllers;

use App\Models\LearnerModel;
use App\Models\Neo4jModel;
use App\Models\SchoolModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    private int $status_code = 200;
    private Neo4jModel $model;
    private array $signUpParams = [];

    public function __construct(Request $request)
    {
        $label = $request->get('type', '');
        if ($label == LearnerModel::$LABEL) {
            $this->signUpParams = [
                "first_name" => "required",
                "last_name" => "required",
                "email" => "required|email",
                "password" => "required",
            ];
            $this->model = new LearnerModel();
        } else if ($label == SchoolModel::$LABEL) {
            $this->signUpParams = [
                "name" => "required",
                "address" => "required",
                "email" => "required|email",
                "password" => "required",
            ];
            $this->model = new SchoolModel();
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "failed",
                "message" => "validation_error",
                "errors" => 'Unrecognized type.'
            ]);
            exit;
        }
    }

    public function signUp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), $this->signUpParams);
            if ($validator->fails()) {
                return response()->json([
                    "status" => "failed",
                    "message" => "validation_error",
                    "errors" => $validator->errors()
                ]);
            }
            $email = $request->get('email');
            $results = $this->model->getDataByEmail($email);
            if ($results->count()) {
                return response()->json([
                    "status" => "failed",
                    "success" => false,
                    "message" => "Email already registered!"
                ]);
            }
            $this->model->create($request->all());
            $results = $this->model->getDataByEmail($email);
            return response()->json([
                "status" => $this->status_code,
                "success" => true,
                "message" => "Registration completed successfully",
                "data" => $results->first()->get('user')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' File: ' . $e->getFile() . ' line:' . $e->getLine());
            return response()->json([
                "status" => "failed",
                "success" => false,
                "message" => "Failed to register"
            ]);
        }
    }

    public function signIn(Request $request): JsonResponse
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
                    "status" => "failed",
                    "validation_error" => $validator->errors()
                ]);
            }

            $email = $request->get('email');
            $password = $request->get('password');

            $results = $this->model->getDataByEmail($email);
            if ($results->count()) {
                $data = $results->first()->get('user');
                if (password_verify($password, $data['password'])) {
                    return response()->json([
                        "status" => $this->status_code,
                        "success" => true,
                        "message" => "You have logged in successfully",
                        "data" => $data
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
}
