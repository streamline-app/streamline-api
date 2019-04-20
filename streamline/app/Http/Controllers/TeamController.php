<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use function GuzzleHttp\json_encode;

define("APIURL", "http://localhost:8080/api/");

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

        //create priority tags for the team
        for ($i = 1; $i <= 10; $i++) {
            $prio_tag = new \App\Tag([
                'name' => 'priority ' . $i,
                'description' => 'priority tag ' . $i,
                'tasks_completed' => 0,
                'average_time' => 0,
                'average_accuracy' => 0,
                'task_over_to_under' => 0,
                'color' => '#c4c4c4',
                'team' => $team['id'],
                'userID' => 0
            ]);
            $prio_tag->save();
        }

        // send request to data server to create a new user
        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode("user1:abc123")
        );

        $id = $team->id;
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
                'method' => 'POST',
                'header' => $header,
                'content' => json_encode($postBody)
            ),
        ));
        $response = file_get_contents(APIURL.'teams/', false, $c);


        // Add the owner to the team member list
        DB::table('teamassignments')->insert(
            ['user' => $team->owner, 'team' => $team->id]
        );
   //     return response()->json(['messagePTa' => 'success'], 200);
        return $response;
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
}
