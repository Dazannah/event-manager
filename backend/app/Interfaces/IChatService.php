<?php

namespace App\Interfaces;

use App\Models\Chat;
use App\Models\Message;

interface IChatService {
  function handleToAgent(Chat $chat): Chat;
  function getOpenChat($user_id): Chat;
  function sendHelpdeskAgentMessage($validated_data, $user_id): void;
  function sendUserMessage($validated_data, $user_id): Message;
  function sendUserMessageToAI($validated_data, $user_id): Message;
}
