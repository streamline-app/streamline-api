<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use \App\Http\Controllers\UserController;

class InvitationController extends Controller
{
    public function create(Request $request) {
        
        $senderId = $request -> senderId;
        $recipientId = $request -> recipientId;

        $test = \App\Invitation::where('recipient', $recipientId)->where('team', $request -> team)->first();
        if($test) {
            return response()->json(['message' => 'multiple invitations'], 200);
        }

        $test = DB::table('teamassignments')->where('user', '=', $recipientId)->where('team', '=', $request -> team)->first();
        if ($test) {
            return response() -> json(['message' => 'member exists'], 200);
        }

        $s = \App\User::find($senderId);
        $r = \App\User::find($recipientId);
        $invitation = new \App\Invitation;
        $invitation -> sender = $senderId;
        $invitation-> recipient = $recipientId;
        $invitation-> senderEmail = $s -> email;
        $invitation-> recipientEmail = $r -> email;
        $invitation -> message = $request -> inviteMessage;
        $invitation  -> created_at = Carbon::now()->toDateTimeString();
        $invitation -> updated_at = Carbon::now()->toDateTimeString();
        $invitation -> team = $request -> team;
        $invitation -> save();
        return response()->json(['message' => 'success'], 200);
    }

    public function sentInvitations($id) {
        $invitations = DB::table('invitations')
        ->join('teams', 'invitations.team', '=', 'teams.id')
        ->select('invitations.*', 'teams.name')
        ->where('invitations.sender', '=', $id)
        ->get();
        return response() -> json($invitations, 200);
    }

    public function recievedInvitations($id) {
        $invitations = DB::table('invitations')
        ->join('teams', 'invitations.team', '=', 'teams.id')
        ->select('invitations.*', 'teams.name')
        ->where('invitations.recipient', '=', $id)
        ->get();
        return response() -> json($invitations, 200);
    }

    public function acceptInvitation(Request $request) {
        $userId = $request -> userId;
        $teamId = $request -> teamId;
        $invitationId = $request -> invitationId;

        $inv = DB::table('invitations')->where('id', '=', $invitationId)->delete();

        DB::table('teamassignments')->insert(
            ['user' => $userId, 'team' => $teamId]
        );

        return response() -> json(['message' => 'success'], 200);

    }

    public function declineInvitation(Request $request) {
        $userId = $request -> userId;
        $teamId = $request -> teamId;
        $invitationId = $request -> invitationId;
        $inv = DB::table('invitations')->where('id', '=', $invitationId)->delete();

        return response() -> json(['message' => 'success'], 200);

    }
}
