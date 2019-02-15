<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsController extends Controller
{
    /**
     * edit the specified resource in storage based on user input
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //only three fields can be edited by the user, all others are done by app
        DB::table('users')->where('id', '=', $id)->update(
            [
                'settings' => $request -> input('settings')
            ]
        );

        return 201;
    }
        /**
     * Display all tags that belong to the user with userID
     * 
     * @param Request $request 
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        //only three fields can be edited by the user, all others are done by app
        $userID = (int)($request -> userID);
        $user = DB::table('users')->where('userID', '=', $userID)->get();
        $userSettings = user.settings;
        return response()->json($userSettings);
    }
}
