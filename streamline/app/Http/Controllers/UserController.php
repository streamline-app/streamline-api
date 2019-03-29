<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserId($email) {
        $count = \App\User::where('email', $email)->count();
        $user = \App\User::where('email', $email)->first();
        if ($count == 0) {
            return response() -> json('not found', 200);
        }
        return response()->json($user -> id, 200);
    }

    public function getUserEmail($id) {
        $user = \App\User::find($id);
        return response() -> json($user -> email, 200);
    }
}
