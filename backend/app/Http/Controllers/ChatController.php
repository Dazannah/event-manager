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

    public function claimChat(Request $req, Chat $chat) {
        try {
            $updated_chat = $this->chatService->claimChat($chat);

            return response()->json(["chat" => $updated_chat]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function closeChat(Request $req, Chat $chat) {
        try {
            $updated_chat = $this->chatService->closeChat($chat);

            return response()->json(["chat" => $updated_chat]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function getChat(Request $req, Chat $chat) {
        try {
            return response()->json(["chat" => $chat]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function getAllChatDescOrder(Request $req) {
        try {
            $chats = Chat::orderBy('updated_at', 'desc')->get();

            return response()->json(["chats" => $chats]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function handleToAgent(Request $req) {
        try {
            $validated_data = $req->validate([
                "chat_id" => "required|exists:chats,id"
            ]);

            $chat = Chat::where('id', '=', $validated_data['chat_id'])->first();

            if ($chat->chatStatus->technical_name == 'closed')
                return response()->json(["error" => "Chat is closed you can't send message here."]);

            if ($chat->user_id != $req->user()->id)
                return response()->json(["error" => "You don't have permission to write in this chat."]);

            if ($chat->handled_to_agent)
                return response()->json(["error" => "Chat already assigned to agents."]);

            $chat = $this->chatService->handleToAgent($chat);

            return response()->json(["chat" => $chat]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }

    public function getOpenChat(Request $req) {
        try {
            $chat = $this->chatService->getOpenChat($req->user()->id);

            $chat->touch();

            return response()->json(["chat" => $chat]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }
    public function helpdeskMessage(Request $req) {
        try {
            $validated_data = $req->validate([
                'message' => 'required',
                'chat_id' => 'required|integer|exists:chats,id'
            ]);

            $message = $this->chatService->sendHelpdeskAgentMessage($validated_data, $req->user()->id);

            return response()->json(["success" => "Message successfully sent.", "message" => $message]);
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

            if ($chat->chatStatus->technical_name == 'closed')
                return response()->json(["error" => "Chat is closed you can't send message here."]);

            if ($chat->user_id != $req->user()->id)
                return response()->json(["error" => "You don't have permission to write in this chat."]);

            if ($chat->handled_to_agent)
                $message = $this->chatService->sendUserMessage($validated_data, $req->user()->id);
            else
                $message = $this->chatService->sendUserMessageToAI($validated_data, $req->user()->id);

            return response()->json(["success" => "Message successfully sent.", "message" => $message]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json($err);
        }
    }
}
