<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class TeamController extends Controller
{

    public function getTeams($id) {
        $teams = \App\Team::where('owner', $id)->get();
        return response()->json($teams, 200);
    }

    public function create(Request $request) {
        $team = new \App\Team;
        $team -> name = $request -> input('title');
        $team -> owner = $request -> input('userId');
        $team -> description = $request -> input('description');
        $team -> color = $request -> input('color');
        $team -> created_at = Carbon::now()->toDateTimeString();
        $team -> updated_at = Carbon::now()->toDateTimeString();

        $team -> save();
        return response() -> json(['messagePTa'=>'success'], 200);

    }

    public function delete($id) {
        $team = \App\Team::find($id);

        if ($team == null) {
            return response('', 404);
        }

        $team -> delete();
        return response('', 204);
    }
}
