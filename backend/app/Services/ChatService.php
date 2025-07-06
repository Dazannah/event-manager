<?php

namespace App\Services;

use App\Models\Message;
use App\Events\ChatMessageEvent;
use App\Interfaces\IChatService;
use App\Events\HelpdeskMessageEvent;
use App\Interfaces\IAiService;

class ChatService implements IChatService {

  private IAiService $aiService;

  function __construct(IAiService $aiService) {
    $this->aiService = $aiService;
  }

  function sendHelpdeskAgentMessage($validated_data, $user_id): void {
    $message = new Message([
      "text" => $validated_data['message'],
      "user_id" => $user_id,
      "chat_id" => $validated_data['chat_id']
    ]);

    $message->save();
    $message->user;

    broadcast(new HelpdeskMessageEvent($message));
  }

  function sendUserMessage($validated_data, $user_id): void {
    $message = new Message([
      "text" => $validated_data['message'],
      "user_id" => $user_id,
      "chat_id" => $validated_data['chat_id']
    ]);

    $message->save();
    $message->user;

    broadcast(new ChatMessageEvent($message));
  }

  function sendUserMessageToAI($validated_data, $user_id): void {
    $message = new Message([
      "text" => $validated_data['message'],
      "user_id" => $user_id,
      "chat_id" => $validated_data['chat_id']
    ]);

    $message->save();
    $message->user;

    $this->aiService->handleUserMessage($message);
  }
}
