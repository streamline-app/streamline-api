<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::table('tokens')->get();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $token = new \App\Token;
        $token -> userId = $request -> input('userId');
        $token -> token = $request -> input('token');
        $token -> save();
        return $token;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function read($id)
    {
        $token = \App\Token::find($id);
        if ($token == null) {
            return response('', 404);
        } else {
            return $token;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $token = \App\Token::find($id);

        if ($token == null) {
            return response('', 404);
        } 

        $token -> userId = $request -> input('userId');
        $token -> token = $request -> input('token');
        $token -> save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($token)
    {
        $token = \App\Token::where('token', $token) -> first();
        $token -> delete();
    }

    /**
    * Validate a given token and return a user id
    */
    public function validateToken($token) {
        $response = \App\Token::where('token', $token) -> first();
        if ($response) {
            return $response -> userId;
        } else {
            return 0;
        }
    }
}
