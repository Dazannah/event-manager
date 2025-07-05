<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Event;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\ChatMessageEvent;
use App\Events\HelpdeskMessageEvent;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller {

    public function helpdeskMessage(Request $req) {
        try {
            $validated_data = $req->validate([
                'message' => 'required',
                'user_id' => 'required|integer'
            ]);

            $message = new Message([
                "text" => $validated_data['message'],
                "user_id" => $validated_data['user_id'],
                "helpdesk_id" => $req->user()->id
            ]);

            $message->save();

            broadcast(new HelpdeskMessageEvent($message));
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function message(Request $req) {
        try {
            $validated_data = $req->validate([
                'message' => 'required'
            ]);

            $message = new Message([
                "text" => $validated_data['message'],
                "user_id" => $req->user()->id
            ]);

            $message->save();

            broadcast(new ChatMessageEvent($message));
        } catch (Exception $err) {
            return response()->json($err);
        }
    }
}
