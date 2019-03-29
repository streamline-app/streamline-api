<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;

define("APIURL", "http://localhost:8080/api/");

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function deleteUser($id) {
        $user = \App\User::find($id);
        $user -> delete();
        return response() -> json(['response'=>'success'], 200);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function signup(Request $request) {
           $user = new User([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
        $user->save();

        

        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode("user1:abc123")
        );

        $id = $user->id;
        $postBody = array(
            'avgTaskTime' => 0,
            'taskEstFactor' => 0,
            'totalOverTasks' => 0,
            'totalTasksComplete' => 0,
            'totalUnderTasks' => 0,
            'userId' => $id
        );

        $c = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header' => $header,
                'content' => json_encode($postBody)
            ),
        ));
        $response = file_get_contents(APIURL.'users/', false, $c);

        return $response;
        $this -> login($request);
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()->name,
            'id' => auth()->user()->id,
        ]);
    }
}