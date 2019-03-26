<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $invitations = \App\Invitation::where('sender', $id)->get();
        return response() -> json($invitations, 200);
    }

    public function recievedInvitations($id) {
        $invitations = \App\Invitation::where('recipient', $id)->get();
        return response() -> json($invitations, 200);
    }
}
