<?php

namespace App\Interfaces;

interface IChatService {
  function sendHelpdeskAgentMessage($validated_data, $user_id): void;
  function sendUserMessage($validated_data, $user_id): void;
  function sendUserMessageToAI($validated_data, $user_id): void;
}
