<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Event;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\ChatMessageEvent;
use App\Events\HelpdeskMessageEvent;
use App\Interfaces\IChatService;
use App\Models\Chat;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller {

    private IChatService $chatService;

    public function __construct(IChatService $chatService) {
        $this->chatService = $chatService;
    }

    public function helpdeskMessage(Request $req) {
        try {
            $validated_data = $req->validate([
                'message' => 'required',
                'chat_id' => 'required|integer|exists:chats,id'
            ]);

            $this->chatService->sendHelpdeskAgentMessage($validated_data, $req->user()->id);

            return response()->json(["success" => "Message successfully sent."]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function message(Request $req) {
        try {
            $validated_data = $req->validate([
                'message' => 'required',
                'chat_id' => 'required|integer|exists:chats,id'
            ]);

            $chat = Chat::where('id', '=', $validated_data['chat_id'])->first();

            if ($chat->chatStatus->technical_name == 'closed' || $chat->id != $req->user()->id)
                return response()->json(["error" => "You don't have permission to write in this chat."]);

            if ($chat->handled_to_agent)
                $this->chatService->sendUserMessage($validated_data, $req->user()->id);
            else
                $this->chatService->sendUserMessageToAI($validated_data, $req->user()->id);

            return response()->json(["success" => "Message successfully sent."]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }
}
