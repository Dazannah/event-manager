<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use App\Models\Message;
use App\Interfaces\IAiService;
use App\Events\ChatMessageEvent;
use App\Events\RefreshChatEvent;

use App\Interfaces\IChatService;
use App\Events\HelpdeskMessageEvent;
use App\Events\HelpdeskRefreshChats;

class ChatService implements IChatService {

  private IAiService $aiService;

  function __construct(IAiService $aiService) {
    $this->aiService = $aiService;
  }

  function claimChat(Chat $chat): Chat {
    $chat->handled_to_agent = true;
    $chat->save();

    $chat->refresh();

    $chat->user_id;
    $user = User::where('id', '=', $chat->user_id)->first();
    broadcast(new RefreshChatEvent($user));
    broadcast(new HelpdeskRefreshChats());

    return $chat;
  }

  function closeChat(Chat $chat): Chat {
    $chat->chat_status_id = 2;
    $chat->save();

    $chat->refresh();

    $user = User::where('id', '=', $chat->user_id)->first();
    broadcast(new RefreshChatEvent($user));
    broadcast(new HelpdeskRefreshChats());

    return $chat;
  }

  function handleToAgent(Chat $chat): Chat {
    $chat->handled_to_agent = true;
    $chat->save();

    broadcast(new HelpdeskRefreshChats());

    return $chat;
  }

  function getOpenChat($user_id): Chat {
    $open_chat = Chat::where([
      ['user_id', '=', $user_id],
      ['chat_status_id', '=', 1]
    ])->first();

    if ($open_chat == null) {
      $open_chat = Chat::create(
        [
          "user_id" => $user_id
        ]
      );

      $message = new Message([
        "text" => "Hello! How can I help you today?",
        "chat_id" => $open_chat->id
      ]);

      $message->save();
    }

    $open_chat->user;
    $open_chat->messages;

    $open_chat->touch();

    return $open_chat;
  }

  function sendHelpdeskAgentMessage($validated_data, $user_id): Message {
    $message = new Message([
      "text" => $validated_data['message'],
      "user_id" => $user_id,
      "chat_id" => $validated_data['chat_id']
    ]);

    $message->save();
    $message->user;

    broadcast(new HelpdeskMessageEvent($message));

    return $message;
  }

  function sendUserMessage($validated_data, $user_id): Message {
    $message = new Message([
      "text" => $validated_data['message'],
      "user_id" => $user_id,
      "chat_id" => $validated_data['chat_id']
    ]);

    $message->save();
    $message->user;

    broadcast(new ChatMessageEvent($message));

    return $message;
  }

  function sendUserMessageToAI($validated_data, $user_id): Message {
    $message = new Message([
      "text" => $validated_data['message'],
      "user_id" => $user_id,
      "chat_id" => $validated_data['chat_id']
    ]);

    $message->save();
    $message->user;

    broadcast(new ChatMessageEvent($message));

    $this->aiService->handleUserMessage($message);

    return $message;
  }
}
