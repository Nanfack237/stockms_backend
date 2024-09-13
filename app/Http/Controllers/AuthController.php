<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        $userData = json_decode($request->user, true);

        if ($userData) {

            $user = User::find($userData['id']);

            if ($user) {

                $status = 200;
                $response = [
                    'success' => 'Your Details',
                    'user' => $user,
                ];

            } else {
                $status = 422;
                $response = [
                    'error' => 'Error, failed to display information',
                ];
            }

            return response()->json($response, $status);

        } else {
            return response()->json('Unauthorized Access', 401);
        }
    }

    public function list(Request $request)
    {

        $users = User::all();

        if ($users) {
            $status = 200;
            $response = [
                'success' => 'Users',
                'users' => $users,
            ];

        } else {
            $status = 422;
            $response = [
                'error' => 'Error, failed to display information',
            ];
        }

        return response()->json($response, $status);

    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $registerUserData = $validator->validated();
        $user = User::create([
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
            'account_type' => 'admin',
            'status' => 1
        ]);

        if($user){

            $status = 201;
            $response = [
                'success' => 'User registered successfully',
                'user' => $user,
            ];

        } else {

            $status = 422;
            $response = [
                'error' => 'Error, failed to create a user',
            ];

        }

        return response()->json($response, $status);

    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);

        if ($validator->fails()) {
            return $this->error('error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $loginUserData = $validator->validated();

        $user = User::where('email',$loginUserData['email'])->first();
        if(!$user || !Hash::check($loginUserData['password'],$user->password)){
            $status = 401;
            $response = [
                'error' => 'Invalid credentials'
            ];

            return response()->json($response, $status);
        } else {

            $token = Str::random(80);
            $apiToken = Hash::make($token);

            $user->api_token = $apiToken;
            $user->save();

            $status = 200;
            $response = [
                'success' => 'User Connected!',
                'user' => $user,
                'token' => $apiToken
            ];
            $header = [
                'Authorization' => "Bearer {$apiToken}",
            ];

            return response()->json($response, $status, $header);
        }

    }

    public function logout(Request $request){

        $userData = json_decode($request->user, true);
        $token = $request->header('Authorization'); // Check for token in headers

        if (!$token) {

            return response()->json('Unauthorized Access', 401);

        } else {

            $user = User::find($userData['id']);

            $tokenlogout = null;
            $user->api_token = $tokenlogout;
            if($user->save()){
                $status = 200;
                $response = [
                    'success' => 'User logout !',
                ];
            } else {
                $status = 422;
                $response = [
                    'error' => 'Error, failed to log out',
                ];
            }
            return response()->json($response, $status);
        }
    }

}
