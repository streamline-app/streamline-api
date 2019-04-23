<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;
use App\Tag;
use function GuzzleHttp\json_decode;

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

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function deleteUser($id)
    {
        //delete user from laravel system
        $user = \App\User::find($id);
        if ($user != null) {
            $user->delete();
        }
        else {
            return response() -> json('User ID not found', 404);
        }

        /*
         * Now we must delete the user from the analytics engine. This requires first
         * getting the UUID of the user with ID = $id and using that to send the DELETE
         * request.
         */

        //create header for HTTP request(s)
        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("user1:abc123")
        );

        //create body of request for GET-ing UUID
        $getOpts = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'header' => $header,
            ),
        ));

        //send GET request
        $UUID = json_decode(file_get_contents(APIURL . 'users/identity/' . $id, false, $getOpts));

        //construct DELETE request with UUID
        $delOpts = stream_context_create(array(
            'http' => array(
                'method' => 'DELETE',
                'header' => $header,
            ),
        ));

        //send DELETE request
        $delResp = file_get_contents(APIURL . 'users/' . $UUID, false, $delOpts);

        //return success to client
        return response()->json(['response' => 'success', 'analytics' => $delResp], 200);
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

    public function signup(Request $request)
    {
        $user = new User([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
        $user->save();

        //create priority tags for the user
        for ($i = 1; $i <= 10; $i++) {
            $prio_tag = new \App\Tag([
                'name' => 'priority ' . $i,
                'description' => 'priority tag ' . $i,
                'tasks_completed' => 0,
                'average_time' => 0,
                'average_accuracy' => 0,
                'task_over_to_under' => 0,
                'color' => '#c4c4c4',
                'team' => 0,
                'userID' => $user['id']
            ]);
            $prio_tag->save();
        }

        // send request to data server to create a new user
        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("user1:abc123")
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
        $response = file_get_contents(APIURL . 'users/', false, $c);

        return $response;
        $this->login($request);
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
