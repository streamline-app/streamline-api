<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use \App\Mail\ResetPasswordMail;
use \App\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;


class PasswordResetController extends Controller
{

    public function changePassword(Request $request) {
        if ($this -> getPasswordResetTableRow($request)->count() > 0) {
            $user = \App\User::where('email', $request->email)->first();
            $user -> update(['password'=>bcrypt($request -> password)]);
            $this -> getPasswordResetTableRow($request) -> delete();
            return $this -> successResponse();
        } else {
            $this -> failedResponse();
        }
    }

    private function getPasswordResetTableRow($request) {
        return DB::table('password_resets')->where(['email' => $request -> email, 'token' => $request -> token]);
    }

    public function passwordResetLink(Request $request) {
        if (!$this -> validateEmail($request -> email)) {
            return $this -> failedResponse();
        }

        $this -> send($request -> email);
        
    }


    public function send($email) {
        $token = $this -> createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken($email) {
        $old = DB::table('password_resets') -> where('email', $email)->first();
        if ($old) {
            return $old->token;
        }
        $token = str_random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken($token, $email) {
        
        DB::table('password_resets') -> insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail($email) {
        return !!User::where('email', $email) -> first();
    }

    public function failedResponse() {
        $body = '{"response":"Email not found in database"}';
        return response($body, 404);
    }

    public function successResponse() {
        $body = '{"response":"success"}';
        return response($body, 200);
    }
}