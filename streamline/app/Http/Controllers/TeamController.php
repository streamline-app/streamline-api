<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class TeamController extends Controller
{

    public function getTeams($id)
    {
        $teams = \App\Team::where('owner', $id)->get();
        return response()->json($teams, 200);
    }

    public function getTeam($id)
    {
        $team = \App\Team::find($id);
        return response()->json($team, 200);
    }

    public function create(Request $request)
    {
        $team = new \App\Team;
        $team->name = $request->input('title');
        $team->owner = $request->input('userId');
        $team->description = $request->input('description');
        $team->color = $request->input('color');
        $team->created_at = Carbon::now()->toDateTimeString();
        $team->updated_at = Carbon::now()->toDateTimeString();

        $team->save();

        // Add the owner to the team member list
        DB::table('teamassignments')->insert(
            ['user' => $team->owner, 'team' => $team->id]
        );
        return response()->json(['messagePTa' => 'success'], 200);
    }

    public function update(Request $request, $id)
    {
        $team = \App\Team::find($id);

        if ($team == null) {
            return response('', 404);
        }

        $team->name = $request->get('title');
        $team->description = $request->get('description');
        $team->color = $request->get('color');

        $team->save();
        return response()->json('success', 200);
    }

    public function delete($id)
    {
        $team = \App\Team::find($id);

        if ($team == null) {
            return response('', 404);
        }

        $team->delete();

        DB::table('tasks')->where('team', '=', $id)->delete();
        return response('', 204);
    }

    public function getTeamMembers($id)
    {
        $members = DB::table('teamassignments')
            ->join('users', 'teamassignments.user', '=', 'users.id')
            ->where('teamassignments.team', '=', $id)
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        return response()->json($members, 200);
    }

    public function getTeamsForUser($id)
    {
        $teams = DB::table('teamassignments')
            ->join('teams', 'teamassignments.team', '=', 'teams.id')
            ->where('teamassignments.user', '=', $id)
            ->select('teams.*')
            ->get();

        return $teams;
    }

    public function leaveTeam(Request $request)
    {
        DB::table("teamassignments")->where('team', '=', $request->team)->where('user', '=', $request->user)->delete();

        $teamMembers = DB::table("teamassignments")->where('team', '=', $request->team)->count();
        if ($teamMembers == 0) {
            $this->delete($request->team);
        }
        return response()->json(['message' => 'success'], 200);
    }

    public function upload(Request $request)
    {
        $path = Storage::disk('local')->putFile('public', $request->file('upload'));

        $id = (int)$request->input('teamID');

        $team = \App\Team::find($id);

        if ($team == null) {
            return response()->json(['team not found', $id], 404);
        }

        $doc = new \App\Document;
        $doc->path = $path;
        $doc->name = $request->file('upload')->getClientOriginalName();
        $doc->team_id = $team->id;
        $doc->save();

        return response()->json(['docID' => $doc->id, 'path' => $path, 'name' => $doc->name], 201);
    }

    public function indexFiles($id)
    {

        $team = \App\Team::find($id);

        if ($team == null) {
            return response()->json(['team not found', $id], 404);
        }


        //list of Document entities for this team
        $teamFiles = $team->documents;

        $names = [];
        foreach ($teamFiles as $row) {
            $fileName = $row->name;
            //    $file = Storage::disk('local')->get($fileName);

            array_push($names, ([$fileName, $row->id]));
        }

        return response()->json($names, 200);
    }

    public function download($id)
    {
        $doc = \App\Document::find($id);

        if ($doc == null) {
            return response()->json(['doc not found', $id], 404);
        }

        $path = $doc->path;

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Authorization , Access-Control-Request-Headers',
            'Content-Type' => 'application/pdf',
        ];

        return Storage::disk('local')->download($path, $doc->name, $headers);
    }
}
