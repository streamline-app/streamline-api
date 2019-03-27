<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamController extends Controller
{

    public function getTeams($id) {
        $teams = \App\Team::where('owner', $id)->get();
        return response()->json($teams, 200);
    }

    public function getTeam($id) {
        $team = \App\Team::find($id);
        return response()->json($team, 200);
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

        // Add the owner to the team member list
        DB::table('teamassignments')->insert(
            ['user' => $team->owner, 'team' => $team->id]
        );
        return response() -> json(['messagePTa'=>'success'], 200);

    }

    public function update(Request $request, $id)
    {
        $team = \App\Team::find($id);

        if($team == null) {
            return response('', 404);
        } 

        $team -> name = $request -> get('title');
        $team -> description = $request -> get('description');
        $team -> color = $request -> get('color');

        $team -> save();
        return response() -> json('success', 200);
    }

    public function delete($id) {
        $team = \App\Team::find($id);

        if ($team == null) {
            return response('', 404);
        }

        $team -> delete();
        return response('', 204);
    }

    public function getTeamMembers($id) {
        $members = DB::table('teamassignments')
        ->join('users', 'teamassignments.user', '=', 'users.id')
        ->where('teamassignments.team', '=', $id)
        ->select('users.name', 'users.email')
        ->get();

        return response() -> json($members, 200);
    }

    public function getTeamsForUser($id) {
        $teams = DB::table('teamassignments')
        ->join('teams', 'teamassignments.team', '=', 'teams.id')
        ->where('teamassignments.user', '=', $id)
        ->select('teams.*')
        ->get();

        return $teams;
    }

    public function leaveTeam(Request $request) {
         DB::table("teamassignments")->where('team', '=', $request -> team)->where('user', '=', $request -> user)->delete();

         return response() -> json(['message'=>'success'], 200);

    }
    
}
