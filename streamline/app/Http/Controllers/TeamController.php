<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use \App\Mail\TransferOwnershipMail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;


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
            'Authorization: Basic ' . base64_encode("user1:abc123")
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
        $response = file_get_contents(APIURL . 'teams/', false, $c);


        // Add the owner to the team member list
        DB::table('teamassignments')->insert(
            ['user' => $team->owner, 'team' => $team->id, 'admin' => 'true']
        );

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
        /**
         * Delete Team from Laravel system, and cascade delete across any tasks and tags
         * associated with the team
         */
        $team = \App\Team::find($id);

        if ($team == null) {
            return response('', 404);
        }

        $team->delete();

        //cascade delete in Tasks and Tag tables
        DB::table('tasks')->where('team', '=', $id)->delete();
        DB::table('tags')->where('team', '=', $id)->delete();

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
        $UUID = json_decode(file_get_contents(APIURL . 'teams/identity/' . $id, false, $getOpts));

        //construct DELETE request with UUID
        $delOpts = stream_context_create(array(
            'http' => array(
                'method' => 'DELETE',
                'header' => $header,
            ),
        ));

        //send DELETE request
        $delResp = file_get_contents(APIURL . 'users/' . $UUID, false, $delOpts);

        return response('', 204);
    }

    public function getTeamMembers($id)
    {
        $members = DB::table('teamassignments')
            ->join('users', 'teamassignments.user', '=', 'users.id')
            ->where('teamassignments.team', '=', $id)
            ->select('users.id', 'users.name', 'users.email', 'teamassignments.admin')
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

    public function promoteTeamMember(Request $request) {
        $userId = $request -> id;
        $teamId = $request -> teamId;
        $promotion = $request -> promotion; 

        DB::table("teamassignments")->where('team', '=', $teamId)->where('user', '=', $userId)->update(['admin' => 'true']);

        return response()->json(['message' => 'success'], 200);
    }

    public function demoteTeamMember(Request $request) {
        $userId = $request -> id;
        $teamId = $request -> teamId;
        $promotion = $request -> promotion; 

        DB::table("teamassignments")->where('team', '=', $teamId)->where('user', '=', $userId)->update(['admin' => 'false']);

        return response()->json(['message' => 'success'], 200);
    }

    public function transferOwnership(Request $request) {
        $previousOwner = $request -> previous;
        $newOwner = $request -> newOwner;
        $team = $request -> team;
        DB::table("teams")->where('id', '=', $team)->update(['owner' => $newOwner]);
        $t = DB::table("teams")->where('id', '=', $team)->first();
        DB::table("teamassignments")->where('team', '=', $team)->where('user', '=', $newOwner)->update(['admin' => 'true']);
        DB::table("teamassignments")->where('team', '=', $team)->where('user', '=', $previousOwner)->update(['admin' => 'true']);

        $user = DB::table('users')->where('id', '=', $newOwner)->first();

        $this -> sendOwnershipTransferMail($user -> email, $t -> name);

        return response()->json(['message' => 'success'], 200);


    }

    public function checkAdmin(Request $request) {
        $userId = $request -> id;
        $teamId = $request -> teamId;

        $result = DB::table("teamassignments")->where('team', '=', $teamId)->where('user', '=', $userId)->pluck('admin');

        return $result;
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

    public function sendOwnershipTransferMail($recipientEmail, $team) {
        Mail::to($recipientEmail)->send(new TransferOwnershipMail($team));
    }
}
