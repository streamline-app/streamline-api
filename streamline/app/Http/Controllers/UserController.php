<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserId($email) {
        $user = \App\User::where('email', $email)->first();
        return response()->json($user -> id, 200);
    }

    public function getUserEmail($id) {
        $user = \App\User::find($id);
        return response() -> json($user -> email, 200);
    }
}
